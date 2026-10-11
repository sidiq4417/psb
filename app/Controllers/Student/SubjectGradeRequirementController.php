<?php

declare(strict_types=1);

namespace App\Controllers\Student;

use App\Models\AdmissionOfferingModel;
use App\Models\RegistrationModel;
use App\Models\RegistrationSubjectGradeModel;
use App\Traits\DataTrait;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class SubjectGradeRequirementController extends BaseController
{
    protected int $actor = 0;

    use DataTrait;

    public function __construct()
    {
        $this->actor = session()->get('actor') ?? 0;
    }

    public function index(): string
    {
        $vars['title'] = 'Persyaratan Nilai';
        $vars['breadcrumb'] = 'Persyaratan Nilai';
        return view('students/subject-grade-requirements', $vars);
    }

    /**
     * List the required subjects with the student's scores.
     *
     * Grades are stored per registration, so a subject is only editable when it
     * is required by a registration that has not been finalised yet and whose
     * admission period is still open. Editable subjects show the score of those
     * registrations, locked subjects the score stored on the locked ones. The
     * response also lists every registration with its lock status. An optional
     * registration_id limits the list to one of the student's registrations
     * (used by the confirmation summary).
     *
     * @return ResponseInterface JSON with records and registrations.
     */
    public function lists(): ResponseInterface
    {
        $registrations = new RegistrationModel()->findActiveByStudent((int)session()->get('actor'));

        $registration_id = (int)($this->request->getVar('registration_id') ?? 0);
        if ($registration_id > 0) {
            $registrations = array_values(array_filter($registrations, fn($reg) => (int)$reg->id === $registration_id));
        }
        if (empty($registrations)) {
            return $this->response->setJSON(['records' => [], 'registrations' => []]);
        }

        $states = $this->describeRegistrations($registrations);
        $offering_ids = array_column($states, 'offering_id');
        $editable_states = array_values(array_filter($states, fn($state) => $state['editable']));

        $gradeModel = new RegistrationSubjectGradeModel();
        $editable_requirements = array_column($gradeModel->findRequirementList(array_column($editable_states, 'offering_id')), null, 'subject_id');

        $editable_scores = [];
        $locked_scores = [];
        foreach ($states as $state) {
            foreach ($gradeModel->findByRegistrationId($state['id']) as $grade) {
                $subject_id = (int)$grade->subject_id;
                if ($state['editable']) {
                    $editable_scores[$subject_id] ??= (float)$grade->score;
                } else {
                    $locked_scores[$subject_id] ??= (float)$grade->score;
                }
            }
        }

        $records = [];
        foreach ($gradeModel->findRequirementList($offering_ids) as $item) {
            $subject_id = $item['subject_id'];
            $editable = isset($editable_requirements[$subject_id]);
            if ($editable) {
                $item['threshold_score'] = $editable_requirements[$subject_id]['threshold_score'];
            }
            $item['score'] = $editable ? ($editable_scores[$subject_id] ?? 0.0) : ($locked_scores[$subject_id] ?? 0.0);
            $item['editable'] = $editable;
            $records[] = $item;
        }

        return $this->response->setJSON([
            'records' => $records,
            'registrations' => $states,
        ]);
    }

    public function save(): ResponseInterface
    {
        $input = [
            'subject_id' => trim((string)($this->request->getVar('subject_id') ?? '')),
            'score' => trim((string)($this->request->getVar('score') ?? '')),
        ];

        $validation = service('validation');
        $validation->setRules([
            'subject_id' => [
                'label' => 'Mata Pelajaran',
                'rules' => 'required|is_natural_no_zero',
                'errors' => ['required' => 'Mata pelajaran tidak valid.', 'is_natural_no_zero' => 'Mata pelajaran tidak valid.'],
            ],
            'score' => [
                'label' => 'Nilai',
                'rules' => ['required', 'regex_match[/^\d{1,3}(\.\d{1,2})?$/]', 'less_than_equal_to[100]'],
                'errors' => [
                    'required' => 'Nilai wajib diisi.',
                    'regex_match' => 'Nilai harus berupa angka 0 sampai 100 dengan maksimal 2 angka desimal.',
                    'less_than_equal_to' => 'Nilai tidak boleh lebih dari 100.',
                ],
            ],
        ]);

        if (!$validation->run($input)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => implode(' ', $validation->getErrors()),
                'errors' => $validation->getErrors(),
            ]);
        }

        $subject_id = (int)$input['subject_id'];
        $score = round((float)$input['score'], 2);

        $registrationModel = new RegistrationModel();
        $registrations = $registrationModel->findActiveByStudent((int)session()->get('actor'));
        if (empty($registrations)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data pendaftaran tidak ditemukan.'
            ]);
        }

        // Grades are stored per registration: finalised registrations and those whose
        // admission period has closed are locked, the others still accept changes.
        $unsubmitted = array_values(array_filter($registrations, fn($reg) => (int)($reg->submitted ?? 0) !== 1));
        if (empty($unsubmitted)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data pendaftaran sudah disubmit dan tidak dapat diubah lagi.'
            ]);
        }

        $offeringModel = new AdmissionOfferingModel();
        $targets = array_values(array_filter($unsubmitted, fn($reg) => $offeringModel->isOpen((int)($reg->admission_offering_id ?? 0))));
        if (empty($targets)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Periode pendaftaran sudah ditutup. Nilai rapor tidak dapat diubah lagi.'
            ]);
        }

        $gradeModel = new RegistrationSubjectGradeModel();
        $offering_ids = array_map(fn($reg) => (int)$reg->admission_offering_id, $targets);

        $requirement = null;
        foreach ($gradeModel->findRequirementList($offering_ids) as $m) {
            if ($m['subject_id'] === $subject_id) {
                $requirement = $m;
                break;
            }
        }
        if ($requirement === null) {
            $all_offering_ids = array_map(fn($reg) => (int)($reg->admission_offering_id ?? 0), $registrations);
            $locked_only = in_array($subject_id, array_column($gradeModel->findRequirementList($all_offering_ids), 'subject_id'), true);
            return $this->response->setJSON([
                'success' => false,
                'message' => $locked_only
                    ? 'Nilai mata pelajaran ini sudah dikunci karena hanya dipersyaratkan oleh pendaftaran yang sudah dikirim final atau periodenya sudah ditutup.'
                    : 'Mata pelajaran tidak termasuk persyaratan program yang dipilih.'
            ]);
        }

        foreach ($targets as $reg) {
            $gradeModel->upsert((int)$reg->id, $subject_id, $score, (float)$requirement['threshold_score']);
        }

        return $this->response->setJSON([
            'success' => true,
            'errors' => [],
            'score' => $score,
        ]);
    }

    /**
     * Describe the student's registrations and whether each one can still be edited.
     *
     * A registration is locked once it has been finalised ("Kirim Final") or when
     * the admission period of its offering has closed.
     *
     * @param array<int, object> $registrations The student's active registrations.
     * @return array<int, array{id: int, offering_id: int, label: string, registration_number: string, submitted: bool, editable: bool, status: string}>
     */
    private function describeRegistrations(array $registrations): array
    {
        $registrationModel = new RegistrationModel();
        $offeringModel = new AdmissionOfferingModel();

        $states = [];
        foreach ($registrations as $reg) {
            $id = (int)$reg->id;
            $offering_id = (int)($reg->admission_offering_id ?? 0);
            $submitted = (int)($reg->submitted ?? 0) === 1;
            $open = !$submitted && $offering_id > 0 && $offeringModel->isOpen($offering_id);

            $details = $registrationModel->findDetailsById($id)->getRow();
            $parts = array_filter([
                (string)($details->branch ?? ''),
                (string)($details->entry_grade ?? ''),
                (string)($details->education_track ?? ''),
                (string)($details->education_program ?? ''),
            ], fn($v) => $v !== '');

            $states[] = [
                'id' => $id,
                'offering_id' => $offering_id,
                'label' => empty($parts) ? 'Pendaftaran #' . $id : implode(' - ', $parts),
                'registration_number' => $submitted ? (string)($reg->registration_number ?? '') : '',
                'submitted' => $submitted,
                'editable' => $open,
                'status' => match (true) {
                    $submitted => 'Sudah dikirim final',
                    !$open => 'Periode pendaftaran ditutup',
                    default => 'Dapat diubah',
                },
            ];
        }
        return $states;
    }
}
