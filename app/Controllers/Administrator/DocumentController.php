<?php

declare(strict_types=1);

namespace App\Controllers\Administrator;

use App\Models\AdmissionDocumentModel;
use App\Traits\DataTrait;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use ReflectionException;

/**
 * Class DocumentController
 *
 * Handles the management of admission document types and requirements.
 *
 * @package App\Controllers\Administrator
 */
class DocumentController extends BaseController
{
    /**
     * Database table name used by QBase.
     *
     * @var string
     */
    protected string $table = 'admission_documents';

    /**
     * Primary key identifier for the current document.
     *
     * @var int
     */
    protected int $id = 0;

    use DataTrait;

    /**
     * DocumentController Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->id = (int)(service('request')->getVar('id') ?? 0);
    }

    /**
     * Render the main Admission Documents page.
     *
     * @return string Rendered HTML view.
     */
    public function index(): string
    {
        return view('administrator/documents', [
            'title' => 'Dokumen Persyaratan'
        ]);
    }

    /**
     * Return a list of admission documents in JSON format.
     *
     * @return ResponseInterface JSON response containing records.
     */
    public function lists(): ResponseInterface
    {
        $model = new AdmissionDocumentModel();
        return $this->response->setJSON([
            'records' => $model->findAll()
        ]);
    }

    /**
     * Save a new admission document or update an existing one.
     *
     * @return ResponseInterface JSON response indicating success or failure.
     * @throws ReflectionException
     */
    public function save(): ResponseInterface
    {
        $validation = service('validation');

        $validation->setRules([
            'name' => [
                'label' => 'Nama Dokumen',
                'rules' => 'required|max_length[255]',
            ],
            'max_size' => [
                'label' => 'Ukuran Maksimum Dokumen (KB)',
                'rules' => 'required|is_natural|less_than_equal_to[51200]',
            ],
        ]);

        if (!$validation->run($this->request->getPost())) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $validation->getErrors(),
            ]);
        }

        $id = (int)($this->request->getVar('id') ?? 0);
        $model = new AdmissionDocumentModel();
        $actor = (int)session()->get('actor');
        $name = (string)$this->request->getPost('name');
        $max_size = (int)$this->request->getPost('max_size');
        $unique_id = 'admission_document:' . url_title(mb_strtolower($name));

        if ($id > 0) {
            $existing = $model->where('unique_id', $unique_id)->where('id !=', $id)->first();
            if ($existing) {
                return $this->response->setJSON([
                    'id' => 0,
                    'success' => false,
                    'message' => 'Data dengan kombinasi ini sudah ada.',
                ]);
            }

            $model->update($id, [
                'name' => $name,
                'max_size' => $max_size,
                'unique_id' => $unique_id,
                'updated_by' => $actor,
            ]);
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

        $insertId = $model->insert([
            'name' => $name,
            'max_size' => $max_size,
            'unique_id' => $unique_id,
            'created_by' => $actor,
        ]);
        return $this->response->setJSON([
            'id' => $insertId ? (int)$insertId : 0,
            'success' => (bool)$insertId,
            'message' => $insertId ? 'Data berhasil disimpan.' : 'Terjadi kesalahan saat menyimpan data.',
        ]);
    }
}
