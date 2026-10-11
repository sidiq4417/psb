<?php

declare(strict_types=1);

namespace App\Controllers\Administrator;

use App\Models\AdmissionDocumentModel;
use App\Models\AdmissionOfferingModel;
use App\Models\DocumentRequirementModel;
use App\Traits\DataTrait;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use ReflectionException;

/**
 * Class SpecificDocumentRequirementController
 *
 * Handles the management of specific document requirements per admission offering.
 *
 * @package App\Controllers\Administrator
 */
class SpecificDocumentRequirementController extends BaseController
{
    protected string $table = 'offering_document_requirements';
    protected int $id = 0;
    use DataTrait;

    /**
     * SpecificDocumentRequirementController Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $request = Services::request();
        $this->id = (int)($request->getPost('id') ?? 0);
    }

    /**
     * Render the specific document requirements page.
     *
     * @param int $offering_id Optional offering ID to filter by.
     * @return string Rendered HTML view.
     */
    public function index(int $offering_id = 0): string
    {
        $docModel = new AdmissionDocumentModel();
        $documents = [];
        foreach ($docModel->findAll() as $row) {
            $documents[$row->id] = $row->name;
        }
        $offeringModel = new AdmissionOfferingModel();
        $vars = [
            'title' => 'Persyaratan Khusus Dokumen',
            'documents' => json_encode($documents, self::REQUIRED_FLAGS),
            'offering_id' => $offering_id,
            'offering' => $offeringModel->findById($offering_id),
        ];
        return view('administrator/specific-document-requirements', $vars);
    }

    /**
     * Return a list of specific document requirements in JSON format.
     *
     * @return ResponseInterface JSON response containing records.
     */
    public function lists(): ResponseInterface
    {
        $offering_id = (int)($this->request->getPost('offering_id') ?? 0);
        $model = new DocumentRequirementModel();
        $records = $model->findAllByOffering($offering_id)->getResult();
        foreach ($records as $r) {
            $r->required = (bool)$r->required;
        }
        return $this->response->setJSON(['records' => $records]);
    }

    /**
     * Save or update a specific document requirement record.
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
        $model = new DocumentRequirementModel();
        $actor = (int)session()->get('actor');
        $admission_offering_id = (int)$this->request->getPost('admission_offering_id');
        $document_id = (int)$this->request->getPost('document_id');
        $data = [
            'admission_offering_id' => $admission_offering_id,
            'document_id' => $document_id,
            // FormBuilder posts checkbox values as "true"/"false".
            'required' => filter_var($this->request->getPost('required'), FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
        ];
        $unique_id = 'offering_document_requirement:' . $admission_offering_id . ':' . $document_id;

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
