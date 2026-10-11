<?php

declare(strict_types=1);

namespace App\Controllers\Student;

use App\Models\AcademicYearModel;
use App\Models\GeneralSubjectGradeRequirementModel;
use App\Models\RegistrationModel;
use App\Models\RegistrationSubjectGradeModel;
use App\Models\SubjectGradeRequirementModel;
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

        $unique_subject_requirements = $this->findSubjectGradeRequirements($offering_ids);

        $reg_ids = array_map(fn($r) => (int)$r->id, $registrations);
        $gradeModel = new RegistrationSubjectGradeModel();
        $all_existing = [];
        foreach ($reg_ids as $rid) {
            foreach ($gradeModel->findByRegistrationId($rid) as $g) {
                $all_existing[] = $g;
            }
        }

        foreach ($unique_subject_requirements as &$item) {
            foreach ($all_existing as $es) {
                if ((int)$es->subject_id === (int)$item['subject_id']) {
                    $item['score'] = (int)$es->score;
                    break;
                }
            }
        }

        $res['records'] = array_map(fn($r) => array_merge($r, ['submitted' => $all_submitted]), $unique_subject_requirements);
        return $this->response->setJSON($res);
    }

    private function findSubjectGradeRequirements(array $offering_ids = []): array
    {
        $subject_requirements = [];
        $generalModel = new GeneralSubjectGradeRequirementModel();
        $specificModel = new SubjectGradeRequirementModel();

        $academic_year_id = $this->getAcademicYearId($this->getAdmissionYear());

        if ($academic_year_id > 0) {
            $generalQuery = $generalModel->findAllByAcademicYear($academic_year_id);
            if ($generalQuery->getNumRows() > 0) {
                foreach ($generalQuery->getResult() as $row) {
                    $subject_requirements[] = [
                        'subject_id' => (int)$row->subject_id,
                        'subject_name' => $row->subject_name,
                        'score' => 0,
                        'threshold_score' => (int)$row->threshold_score,
                    ];
                }
            }
        }

        foreach ($offering_ids as $oid) {
            if ($oid <= 0) continue;
            $specificQuery = $specificModel->findByAdmissionOfferingId($oid);
            if ($specificQuery->getNumRows() > 0) {
                foreach ($specificQuery->getResult() as $row) {
                    $subject_requirements[] = [
                        'subject_id' => (int)$row->subject_id,
                        'subject_name' => $row->subject_name,
                        'score' => 0,
                        'threshold_score' => (int)$row->threshold_score,
                    ];
                }
            }
        }

        $unique_subject_requirements = [];
        $seenSubjectIds = [];
        foreach ($subject_requirements as $item) {
            if (!in_array($item['subject_id'], $seenSubjectIds)) {
                $seenSubjectIds[] = $item['subject_id'];
                $unique_subject_requirements[] = $item;
            }
        }
        return $unique_subject_requirements;
    }

    public function save(): ResponseInterface
    {
        $subject_id = (int)($this->request->getVar('subject_id') ?? 0);
        $score = (int)($this->request->getVar('score') ?? 0);

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

        if ($subject_id <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Mata pelajaran tidak valid.'
            ]);
        }

        $offering_ids = [];
        foreach ($registrations as $reg) {
            $oid = (int)($reg->admission_offering_id ?? 0);
            if ($oid > 0) $offering_ids[] = $oid;
        }

        $threshold = 0;
        $master = $this->findSubjectGradeRequirements($offering_ids);
        foreach ($master as $m) {
            if ((int)$m['subject_id'] === $subject_id) {
                $threshold = (int)$m['threshold_score'];
                break;
            }
        }

        $gradeModel = new RegistrationSubjectGradeModel();
        foreach ($registrations as $reg) {
            $reg_id = (int)$reg->id;
            $gradeModel->upsert($reg_id, $subject_id, $score, $threshold);
        }

        return $this->response->setJSON([
            'success' => true,
            'errors' => [],
        ]);
    }

    private function getAdmissionYear(): int
    {
        return new RegistrationModel()->getAdmissionYearByStudent((int)session()->get('actor'));
    }

    private function getAcademicYearId(int $admission_year): int
    {
        return new AcademicYearModel()->findIdByNameLike((string)$admission_year);
    }
}
