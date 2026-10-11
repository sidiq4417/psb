<?php

declare(strict_types=1);

namespace App\Controllers\Administrator;

use App\Models\BranchModel;
use App\Traits\DataTrait;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use ReflectionException;

/**
 * Class BranchController
 *
 * Handles the management of campus branches.
 *
 * @package App\Controllers\Administrator
 */
class BranchController extends BaseController
{
    /**
     * Database table name.
     *
     * @var string
     */
    protected string $table = 'branches';

    /**
     * Primary key ID for the branch record.
     *
     * @var int
     */
    protected int $id = 0;

    use DataTrait;

    /**
     * BranchController Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->id = (int)(service('request')->getVar('id') ?? 0);
    }

    /**
     * Display the branches management page.
     *
     * @return string Rendered HTML view.
     */
    public function index(): string
    {
        $vars = [
            'title' => 'Sekolah',
            'educationLevels' => ['TK', 'RA', 'SD', 'MI', 'SMP', 'MTs', 'SMA', 'MA', 'SMK'],
        ];
        return view('administrator/branches', $vars);
    }

    /**
     * List all branch records.
     *
     * @return ResponseInterface JSON response containing records.
     */
    public function lists(): ResponseInterface
    {
        $model = new BranchModel();
        return $this->response->setJSON([
            'records' => $model->findAll()
        ]);
    }

    /**
     * Save or update a branch record.
     *
     * @return ResponseInterface JSON response indicating success or failure.
     * @throws ReflectionException
     */
    public function save(): ResponseInterface
    {
        $rules = [
            'code' => ['label' => 'Kode Sekolah', 'rules' => 'required|max_length[50]'],
            'name' => ['label' => 'Nama Sekolah', 'rules' => 'required|max_length[255]'],
            'education_level' => ['label' => 'Jenjang Pendidikan', 'rules' => 'required|in_list[TK,RA,SD,MI,SMP,MTs,SMA,MA,SMK]'],
            'address' => ['label' => 'Alamat', 'rules' => 'permit_empty|max_length[1000]'],
            'phone' => ['label' => 'Telepon', 'rules' => 'permit_empty|max_length[50]'],
            'email' => ['label' => 'Email', 'rules' => 'permit_empty|valid_email|max_length[255]'],
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors(),
            ]);
        }

        $id = (int)($this->request->getVar('id') ?? 0);
        $model = new BranchModel();
        $actor = (int)session()->get('actor');
        $name = (string)$this->request->getPost('name');
        $data = [
            'code' => (string)$this->request->getPost('code'),
            'name' => $name,
            'education_level' => (string)$this->request->getPost('education_level'),
            'address' => (string)$this->request->getPost('address'),
            'phone' => (string)$this->request->getPost('phone'),
            'email' => (string)$this->request->getPost('email'),
        ];
        $unique_id = 'branch:' . url_title(mb_strtolower($name));

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
