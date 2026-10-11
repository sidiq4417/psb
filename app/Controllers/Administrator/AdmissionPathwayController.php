<?php

declare(strict_types=1);

namespace App\Controllers\Administrator;

use App\Models\AdmissionPathwayModel;
use App\Traits\DataTrait;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use ReflectionException;

/**
 * Class AdmissionPathwayController
 *
 * Handles the management of admission paths for prospective students.
 *
 * @package App\Controllers\Administrator
 */
class AdmissionPathwayController extends BaseController
{
    /**
     * Database table name.
     *
     * @var string
     */
    protected string $table = 'admission_pathways';

    /**
     * Primary key ID for the branch record.
     *
     * @var int
     */
    protected int $id = 0;

    use DataTrait;

    /**
     * AdmissionPathwayController Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->id = (int) (service('request')->getVar('id') ?? 0);
    }

    /**
     * Display the admission paths management page.
     *
     * @return string Rendered HTML view.
     */
    public function index(): string
    {
        $vars = [
            'title' => 'Jalur Masuk',
        ];
        return view('administrator/admission-pathways', $vars);
    }

    /**
     * List all admission path records.
     *
     * @return ResponseInterface JSON response containing records.
     */
    public function lists(): ResponseInterface
    {
        $model = new AdmissionPathwayModel();
        return $this->response->setJSON([
            'records' => $model->findAll()
        ]);
    }

    /**
     * Save or update an admission path record.
     *
     * @return ResponseInterface JSON response indicating success or failure.
     * @throws ReflectionException
     */
    public function save(): ResponseInterface
    {
        $rules = [
            'name' => ['label' => 'Jalur Masuk', 'rules' => 'required|max_length[255]']
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors(),
            ]);
        }

        $id = (int)($this->request->getVar('id') ?? 0);
        $model = new AdmissionPathwayModel();
        $actor = (int)session()->get('actor');
        $name = (string)$this->request->getPost('name');
        $unique_id = 'admission_pathway:' . url_title(mb_strtolower($name));

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
