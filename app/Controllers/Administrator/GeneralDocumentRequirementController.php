<?php

declare(strict_types=1);

namespace App\Controllers\Administrator;

use App\Models\AcademicYearModel;
use App\Models\AdmissionDocumentModel;
use App\Models\AdmissionPeriodModel;
use App\Models\GeneralDocumentRequirementModel;
use App\Traits\DataTrait;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use ReflectionException;

/**
 * Class GeneralDocumentRequirementController
 *
 * Handles the management of general document requirements per academic year.
 *
 * @package App\Controllers\Administrator
 */
class GeneralDocumentRequirementController extends BaseController
{
    protected string $table = 'general_document_requirements';
    protected int $id = 0;

    use DataTrait;

    /**
     * GeneralDocumentRequirementController Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $request = Services::request();
        $this->id = (int)($request->getPost('id') ?? 0);
    }

    /**
     * Render the general document requirements page.
     *
     * @param int $academic_year_id The academic year ID to filter by.
     * @return string Rendered HTML view.
     */
    public function index(int $academic_year_id = 0): string
    {
        $docModel = new AdmissionDocumentModel();
        $ayModel = new AcademicYearModel();
        $apModel = new AdmissionPeriodModel();
        $admission_periods = [];

        foreach ($apModel->findAll() as $p) {
            if ((int)$p->academic_year_id === $academic_year_id) {
                $admission_periods[] = $p;
            }
        }

        $documents = [];
        foreach ($docModel->findAll() as $row) {
            $documents[$row->id] = $row->name;
        }

        $academic_year = $academic_year_id > 0 ? $ayModel->find($academic_year_id) : null;

        $vars = [
            'title' => 'Persyaratan Umum Dokumen Pendaftaran',
            'documents' => toJson($documents),
            'academic_year_id' => $academic_year_id,
            'academic_year' => $academic_year,
            'admission_periods' => $admission_periods,
        ];
        return view('administrator/general-document-requirements', $vars);
    }

    /**
     * Return a list of general document requirements in JSON format.
     *
     * @return ResponseInterface JSON response containing records.
     */
    public function lists(): ResponseInterface
    {
        $academic_year_id = (int)($this->request->getPost('academic_year_id') ?? 0);
        $model = new GeneralDocumentRequirementModel();
        $records = $model->findAllByAcademicYear($academic_year_id)->getResult();
        foreach ($records as $r) {
            $r->required = (bool)$r->required;
        }
        return $this->response->setJSON([
            'records' => $records
        ]);
    }

    /**
     * Save or update a general document requirement record.
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
            'document_id' => [
                'label' => 'Dokumen',
                'rules' => 'required|is_natural_no_zero|is_not_unique[admission_documents.id]',
                'errors' => ['is_not_unique' => 'Dokumen tidak ditemukan.'],
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
        $document_id = (int)$this->request->getPost('document_id');
        // FormBuilder posts checkbox values as "true"/"false".
        $required = filter_var($this->request->getPost('required'), FILTER_VALIDATE_BOOLEAN);
        $unique_id = 'general_document_requirement:' . $academic_year_id . ':' . $document_id;
        $actor = (int)session()->get('actor');
        $model = new GeneralDocumentRequirementModel();

        if ($id > 0) {
            $existing = $model->where('unique_id', $unique_id)->where('id !=', $id)->first();
            if ($existing) {
                return $this->response->setJSON([
                    'id' => 0,
                    'success' => false,
                    'message' => 'Dokumen ini sudah diatur untuk tahun ajaran tersebut.',
                ]);
            }

            $model->update($id, [
                'unique_id' => $unique_id,
                'academic_year_id' => $academic_year_id,
                'document_id' => $document_id,
                'required' => $required ? 1 : 0,
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
            'document_id' => $document_id,
            'required' => $required ? 1 : 0,
            'created_by' => $actor,
        ]);

        return $this->response->setJSON([
            'id' => $insert_id ? (int)$insert_id : 0,
            'success' => (bool)$insert_id,
            'message' => $insert_id ? 'Saved successfully.' : 'Error occurred.',
        ]);
    }
}
