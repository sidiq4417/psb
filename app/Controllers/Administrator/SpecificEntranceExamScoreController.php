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
                    'message' => 'Unique identifier collision.',
                ]);
            }

            $data['unique_id'] = $unique_id;
            $data['updated_by'] = $actor;
            $model->update($id, $data);
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

        $data['unique_id'] = $unique_id;
        $data['created_by'] = $actor;
        $insertId = $model->insert($data);
        return $this->response->setJSON([
            'id' => $insertId ? (int)$insertId : 0,
            'success' => (bool)$insertId,
            'message' => $insertId ? 'Saved successfully.' : 'Error occurred.',
        ]);
    }
}
