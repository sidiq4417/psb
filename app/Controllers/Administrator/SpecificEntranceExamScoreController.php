<?php

declare(strict_types=1);

namespace App\Controllers\Administrator;

use App\Models\AdmissionOfferingModel;
use App\Models\EntranceExamScoreModel;
use App\Models\SubjectModel;
use App\Traits\DataTrait;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use ReflectionException;

/**
 * Class SpecificEntranceExamScoreController
 *
 * Handles the management of specific entrance exam score requirements per admission offering.
 *
 * @package App\Controllers\Administrator
 */
class SpecificEntranceExamScoreController extends BaseController
{
    protected string $table = 'offering_entrance_exam_scores';
    protected int $id = 0;
    use DataTrait;

    /**
     * SpecificEntranceExamScoreController Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $request = Services::request();
        $this->id = (int)($request->getPost('id') ?? 0);
    }

    /**
     * Render the specific entrance exam scores page.
     *
     * @param int $offering_id Optional offering ID to filter by.
     * @return string Rendered HTML view.
     */
    public function index(int $offering_id = 0): string
    {
        $subjectModel = new SubjectModel();
        $subjects = [];
        foreach ($subjectModel->findAll() as $row) {
            $subjects[$row->id] = $row->name;
        }
        $offeringModel = new AdmissionOfferingModel();
        $vars = [
            'title' => 'Persyaratan Khusus Ujian Saringan Masuk',
            'subjects' => json_encode($subjects, self::REQUIRED_FLAGS),
            'offering_id' => $offering_id,
            'offering' => $offeringModel->findById($offering_id),
        ];
        return view('administrator/specific-entrance-exam-scores', $vars);
    }

    /**
     * Return a list of specific entrance exam scores in JSON format.
     *
     * @return ResponseInterface JSON response containing records.
     */
    public function lists(): ResponseInterface
    {
        $offering_id = (int)($this->request->getPost('offering_id') ?? 0);
        $model = new EntranceExamScoreModel();
        return $this->response->setJSON(['records' => $model->findAllByAdmissionOffering($offering_id)->getResult()]);
    }

    /**
     * Save or update a specific entrance exam score record.
     *
     * @return ResponseInterface JSON response indicating success or failure.
     * @throws ReflectionException
     */
    public function save(): ResponseInterface
    {
        $rules = [
            'admission_offering_id' => [
                'label' => 'Pembukaan Pendaftaran',
                'rules' => 'required|is_natural_no_zero|is_not_unique[admission_offerings.id]',
                'errors' => ['is_not_unique' => 'Pembukaan Pendaftaran tidak ditemukan.'],
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
        $model = new EntranceExamScoreModel();
        $actor = (int)session()->get('actor');
        $admission_offering_id = (int)$this->request->getPost('admission_offering_id');
        $subject_id = (int)$this->request->getPost('subject_id');
        $data = [
            'admission_offering_id' => $admission_offering_id,
            'subject_id' => $subject_id,
            'threshold_score' => (int)$this->request->getPost('threshold_score'),
        ];
        $unique_id = 'offering_entrance_exam_score:' . $admission_offering_id . ':' . $subject_id;

        if ($id > 0) {
            $existing = $model->where('unique_id', $unique_id)->where('id !=', $id)->first();
            if ($existing) {
                return $this->response->setJSON([
                    'id' => 0,
                    'success' => false,
                    'message' => 'Data dengan kombinasi ini sudah ada.',
                ]);
            }

            $data['unique_id'] = $unique_id;
            $data['updated_by'] = $actor;
            $model->update($id, $data);
            return $this->response->setJSON([
                'id' => $id,
                'success' => true,
                'message' => 'Data berhasil diperbarui.',
            ]);
        }

        $existing = $model->where('unique_id', $unique_id)->first();
        if ($existing) {
            return $this->response->setJSON([
                'id' => 0,
                'success' => false,
                'message' => 'Data dengan kombinasi ini sudah ada.',
            ]);
        }

        $data['unique_id'] = $unique_id;
        $data['created_by'] = $actor;
        $insertId = $model->insert($data);
        return $this->response->setJSON([
            'id' => $insertId ? (int)$insertId : 0,
            'success' => (bool)$insertId,
            'message' => $insertId ? 'Data berhasil disimpan.' : 'Terjadi kesalahan saat menyimpan data.',
        ]);
    }
}
