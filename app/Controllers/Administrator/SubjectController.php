<?php

declare(strict_types=1);

namespace App\Controllers\Administrator;

use App\Models\SubjectModel;
use App\Traits\DataTrait;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use ReflectionException;

/**
 * Class SubjectController
 *
 * Handles the management of school subjects used in admission requirements.
 *
 * @package App\Controllers\Administrator
 */
class SubjectController extends BaseController
{
    /**
     * Database table name used by QBase.
     *
     * @var string
     */
    protected string $table = 'subjects';

    /**
     * Primary key identifier for the current subject.
     *
     * @var int
     */
    protected int $id = 0;

    use DataTrait;

    /**
     * SubjectController Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->id = (int)(service('request')->getVar('id') ?? 0);
    }

    /**
     * Render the subject page.
     *
     * @return string Rendered HTML view.
     */
    public function index(): string
    {
        return view('administrator/subjects', [
            'title' => 'Mata Pelajaran'
        ]);
    }

    /**
     * Return a list of subjects in JSON format.
     *
     * @return ResponseInterface JSON response containing records.
     */
    public function lists(): ResponseInterface
    {
        $model = new SubjectModel();
        return $this->response->setJSON([
            'records' => $model->findAll()
        ]);
    }

    /**
     * Save or update a subject record.
     *
     * @return ResponseInterface JSON response indicating success or failure.
     * @throws ReflectionException
     */
    public function save(): ResponseInterface
    {
        $validation = service('validation');

        $validation->setRules([
            'name' => [
                'label' => 'Nama Mata Pelajaran',
                'rules' => 'required|max_length[255]',
            ],
        ]);

        // Defensive: getJsonVar returns single value, not array. Use getJSON(true).
        if (!$validation->run($this->request->getVar())) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $validation->getErrors(),
            ]);
        }

        $id = (int)($this->request->getVar('id') ?? 0);
        $model = new SubjectModel();
        $actor = (int)session()->get('actor');
        $name = (string)$this->request->getPost('name');
        $unique_id = 'subject:' . url_title(mb_strtolower($name));

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
