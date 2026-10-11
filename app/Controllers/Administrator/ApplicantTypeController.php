<?php

declare(strict_types=1);

namespace App\Controllers\Administrator;

use App\Models\ApplicantTypeModel;
use App\Traits\DataTrait;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use ReflectionException;

/**
 * Class ApplicantTypeController
 *
 * Handles the management of registration types (e.g., New Student, Transfer).
 *
 * @package App\Controllers\Administrator
 */
class ApplicantTypeController extends BaseController
{
    /**
     * Database table name.
     *
     * @var string
     */
    protected string $table = 'applicant_types';

    /**
     * Primary key ID for the branch record.
     *
     * @var int
     */
    protected int $id = 0;

    use DataTrait;

    /**
     * ApplicantTypeController Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->id = (int) (service('request')->getVar('id') ?? 0);
    }

    /**
     * Display the registration types management page.
     *
     * @return string Rendered HTML view.
     */
    public function index(): string
    {
        $vars = [
            'title' => 'Jenis Pendaftaran',
        ];
        return view('administrator/applicant-types', $vars);
    }

    /**
     * List all registration type records.
     *
     * @return ResponseInterface JSON response containing records.
     */
    public function lists(): ResponseInterface
    {
        $model = new ApplicantTypeModel();
        return $this->response->setJSON([
            'records' => $model->findAll()
        ]);
    }

    /**
     * Save or update a registration type record.
     *
     * @return ResponseInterface JSON response indicating success or failure.
     * @throws ReflectionException
     */
    public function save(): ResponseInterface
    {
        $rules = [
            'name' => ['label' => 'Jenis Pendaftaran', 'rules' => 'required|max_length[255]']
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors(),
            ]);
        }

        $id = (int)($this->request->getVar('id') ?? 0);
        $model = new ApplicantTypeModel();
        $actor = (int)session()->get('actor');
        $name = (string)$this->request->getPost('name');
        $unique_id = 'applicant_type:' . url_title(mb_strtolower($name));

        if ($id > 0) {
            $existing = $model->where('unique_id', $unique_id)->where('id !=', $id)->first();
            if ($existing) {
                return $this->response->setJSON([
                    'id' => 0,
                    'success' => false,
                    'message' => 'Data dengan kombinasi ini sudah ada.',
                ]);
            }

            $model->update($id, ['name' => $name, 'unique_id' => $unique_id, 'updated_by' => $actor]);
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

        $insertId = $model->insert(['name' => $name, 'unique_id' => $unique_id, 'created_by' => $actor]);
        return $this->response->setJSON([
            'id' => $insertId ? (int)$insertId : 0,
            'success' => (bool)$insertId,
            'message' => $insertId ? 'Data berhasil disimpan.' : 'Terjadi kesalahan saat menyimpan data.',
        ]);
    }
}
