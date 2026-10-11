<?php

declare(strict_types=1);

namespace App\Controllers\Administrator;

use App\Models\AcademicYearModel;
use App\Models\AdmissionPeriodModel;
use App\Models\GeneralEntranceExamScoreModel;
use App\Models\SubjectModel;
use App\Traits\DataTrait;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use ReflectionException;

/**
 * Class GeneralEntranceExamScoreController
 *
 * Handles the management of general entrance exam score requirements per academic year.
 *
 * @package App\Controllers\Administrator
 */
class GeneralEntranceExamScoreController extends BaseController
{
    protected string $table = 'general_entrance_exam_scores';
    protected int $id = 0;

    use DataTrait;

    /**
     * GeneralEntranceExamScoreController Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $request = Services::request();
        $this->id = (int)($request->getPost('id') ?? 0);
    }

    /**
     * Render the general entrance exam scores page.
     *
     * @param int $academic_year_id The academic year ID to filter by.
     * @return string Rendered HTML view.
     */
    public function index(int $academic_year_id = 0): string
    {
        $subjectModel = new SubjectModel();
        $ayModel = new AcademicYearModel();
        $apModel = new AdmissionPeriodModel();
        $admission_periods = [];

        foreach ($apModel->findAll() as $p) {
            if ((int)$p->academic_year_id === $academic_year_id) {
                $admission_periods[] = $p;
            }
        }

        $subjects = [];
        foreach ($subjectModel->findAll() as $row) {
            $subjects[$row->id] = $row->name;
        }

        $academic_year = $academic_year_id > 0 ? $ayModel->find($academic_year_id) : null;

        $vars = [
            'title' => 'Persyaratan Umum Ujian Saringan Masuk',
            'subjects' => toJson($subjects),
            'academic_year_id' => $academic_year_id,
            'academic_year' => $academic_year,
            'admission_periods' => $admission_periods,
        ];

        return view('administrator/general-entrance-exam-scores', $vars);
    }

    /**
     * Return a list of general entrance exam scores in JSON format.
     *
     * @return ResponseInterface JSON response containing records.
     */
    public function lists(): ResponseInterface
    {
        $academic_year_id = (int)($this->request->getPost('academic_year_id') ?? 0);
        $model = new GeneralEntranceExamScoreModel();
        $records = $model->findAllByAcademicYear($academic_year_id)->getResult();
        return $this->response->setJSON([
            'records' => $records
        ]);
    }

    /**
     * Save or update a general entrance exam score record.
     *
     * @return ResponseInterface JSON response indicating success or failure.
     * @throws ReflectionException
     */
    public function save(): ResponseInterface
    {
        $rules = [
            'academic_year_id' => [
                'label' => 'Tahun Ajaran',
                'rules' => 'required|is_natural_no_zero|is_not_unique[academic_years.id]',
                'errors' => ['is_not_unique' => 'Tahun Ajaran tidak ditemukan.'],
            ],
            'subject_id' => [
                'label' => 'Mata Pelajaran',
                'rules' => 'required|is_natural_no_zero|is_not_unique[subjects.id]',
                'errors' => ['is_not_unique' => 'Mata Pelajaran tidak ditemukan.'],
            ],
            'threshold_score' => [
                'label' => 'Nilai Minimum',
                'rules' => 'required|is_natural|less_than_equal_to[100]',
                'errors' => [
                    'is_natural' => 'Nilai Minimum harus berupa bilangan bulat 0-100.',
                    'less_than_equal_to' => 'Nilai Minimum harus berupa bilangan bulat 0-100.',
                ],
            ],
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors(),
            ]);
        }

        $id = (int)($this->request->getPost('id') ?? 0);
        $academic_year_id = (int)$this->request->getPost('academic_year_id');
        $subject_id = (int)$this->request->getPost('subject_id');
        $threshold_score = (int)$this->request->getPost('threshold_score');
        $unique_id = 'general_entrance_exam_score:' . $academic_year_id . ':' . $subject_id;
        $actor = (int)session()->get('actor');
        $model = new GeneralEntranceExamScoreModel();

        if ($id > 0) {
            $existing = $model->where('unique_id', $unique_id)->where('id !=', $id)->first();
            if ($existing) {
                return $this->response->setJSON([
                    'id' => 0,
                    'success' => false,
                    'message' => 'Mata Pelajaran ini sudah diatur untuk tahun ajaran tersebut.',
                ]);
            }

            $model->update($id, [
                'unique_id' => $unique_id,
                'academic_year_id' => $academic_year_id,
                'subject_id' => $subject_id,
                'threshold_score' => $threshold_score,
                'updated_by' => $actor,
            ]);
            return $this->response->setJSON([
                'id' => $id,
                'success' => true,
                'message' => 'Updated successfully.',
            ]);
        }

        $existing = $model->where('unique_id', $unique_id)->first();
        if ($existing) {
            return $this->response->setJSON([
                'id' => 0,
                'success' => false,
                'message' => 'Unique identifier already exists.',
            ]);
        }

        $insert_id = $model->insert([
            'unique_id' => $unique_id,
            'academic_year_id' => $academic_year_id,
            'subject_id' => $subject_id,
            'threshold_score' => $threshold_score,
            'created_by' => $actor,
        ]);

        return $this->response->setJSON([
            'id' => $insert_id ? (int)$insert_id : 0,
            'success' => (bool)$insert_id,
            'message' => $insert_id ? 'Saved successfully.' : 'Error occurred.',
        ]);
    }
}
