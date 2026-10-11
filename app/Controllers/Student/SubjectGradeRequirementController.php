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

    public function lists(): ResponseInterface
    {
        $registrationModel = new RegistrationModel();
        $registrations = $registrationModel->findActiveByStudent((int)session()->get('actor'));
        if (empty($registrations)) {
            return $this->response->setJSON(['records' => []]);
        }

        $offering_ids = [];
        $all_submitted = true;
        foreach ($registrations as $reg) {
            $oid = (int)($reg->admission_offering_id ?? 0);
            if ($oid > 0) $offering_ids[] = $oid;
            if ((int)($reg->submitted ?? 0) !== 1) $all_submitted = false;
        }

        $gradeModel = new RegistrationSubjectGradeModel();
        $unique_subject_requirements = $gradeModel->findRequirementList($offering_ids);

        $reg_ids = array_map(fn($r) => (int)$r->id, $registrations);
        $all_existing = [];
        foreach ($reg_ids as $rid) {
            foreach ($gradeModel->findByRegistrationId($rid) as $g) {
                $all_existing[] = $g;
            }
        }

        foreach ($unique_subject_requirements as &$item) {
            foreach ($all_existing as $es) {
                if ((int)$es->subject_id === (int)$item['subject_id']) {
                    $item['score'] = (float)$es->score;
                    break;
                }
            }
        }
        unset($item);

        $res['records'] = array_map(fn($r) => array_merge($r, ['submitted' => $all_submitted]), $unique_subject_requirements);
        return $this->response->setJSON($res);
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

        if (array_any($registrations, fn($reg) => (int)$reg->submitted === 1)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data pendaftaran sudah disubmit dan tidak dapat diubah lagi.'
            ]);
        }

        $offeringModel = new AdmissionOfferingModel();
        $targets = array_values(array_filter($registrations, fn($reg) => $offeringModel->isOpen((int)($reg->admission_offering_id ?? 0))));
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
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Mata pelajaran tidak termasuk persyaratan program yang dipilih.'
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
}
