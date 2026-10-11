<?php

declare(strict_types=1);

namespace App\Controllers\Administrator;

use App\Models\OperatorModel;
use App\Traits\DataTrait;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use ReflectionException;

/**
 * Class OperatorController
 *
 * Handles the management of system operators and administrative users.
 *
 * @package App\Controllers\Administrator
 */
class OperatorController extends BaseController
{
    /**
     * Database table name used by QBase.
     *
     * @var string
     */
    protected string $table = 'users';

    /**
     * Primary key identifier for the current operator.
     *
     * @var int
     */
    protected int $id = 0;

    use DataTrait;

    /**
     * OperatorController Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $request = Services::request();
        $this->id = (int)($request->getPost('id') ?? 0);
    }

    /**
     * Render the operator page.
     *
     * @return string Rendered HTML view.
     */
    public function index(): string
    {
        return view('administrator/operators', [
            'title' => 'Pengaturan Pengguna'
        ]);
    }

    /**
     * Return a list of operators in JSON format.
     *
     * @return ResponseInterface JSON response containing records.
     */
    public function lists(): ResponseInterface
    {
        $model = new OperatorModel();
        return $this->response->setJSON([
            'records' => $model->findAll()
        ]);
    }

    /**
     * Save or update an operator record.
     *
     * @return ResponseInterface JSON response indicating success or failure.
     * @throws ReflectionException
     */
    public function save(): ResponseInterface
    {
        $validation = service('validation');

        $validation->setRules([
            'full_name' => [
                'label' => 'Full Name',
                'rules' => 'required',
            ],
            'email' => [
                'label' => 'Email',
                'rules' => 'required|valid_email',
            ],
        ]);

        // Defensive: getJsonVar returns single value, not array. Use getJSON(true).
        if (!$validation->run($this->request->getPost())) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $validation->getErrors(),
            ]);
        }

        $id = (int)($this->request->getPost('id') ?? 0);
        $model = new OperatorModel();
        $actor = (int)session()->get('actor');
        $full_name = (string)$this->request->getPost('full_name');
        $unique_id = 'user:' . url_title(mb_strtolower($full_name));

        $data = [
            'full_name' => $full_name,
            'email' => (string)$this->request->getPost('email'),
            'is_operator' => true,
        ];

        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        if ($id > 0) {
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
