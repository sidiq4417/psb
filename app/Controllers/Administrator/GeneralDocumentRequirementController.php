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
            'documents' => json_encode($documents),
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
        $id = (int)($this->request->getPost('id') ?? 0);
        $academic_year_id = (int)$this->request->getPost('academic_year_id');
        $document_id = (int)$this->request->getPost('document_id');
        $required = (bool)$this->request->getPost('required');
        $unique_id = 'general_document_requirement:' . $academic_year_id . ':' . $document_id;
        $actor = (int)session()->get('actor');
        $model = new GeneralDocumentRequirementModel();

        if ($id > 0) {
            $model->update($id, [
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
