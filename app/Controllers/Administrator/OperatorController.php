<?php

declare(strict_types=1);

namespace App\Controllers\Administrator;

use App\Libraries\Remember;
use App\Models\OperatorModel;
use App\Models\UserModel;
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
     * Return the form data of an operator account.
     *
     * Only operator accounts can be loaded here; administrators and applicants are refused.
     *
     * @return ResponseInterface JSON response containing record data.
     */
    public function form(): ResponseInterface
    {
        $vars = ['id' => 0, 'unique_id' => '', 'is_deleted' => 0];

        if ($this->id > 0) {
            if ($this->findOperator($this->id) === null) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Pengguna tidak ditemukan.',
                ]);
            }
            $row = new OperatorModel()->select('id, unique_id, full_name, email, is_deleted')->find($this->id);
            if ($row) {
                $vars = (array)$row;
                $vars['is_deleted'] = (int)($vars['is_deleted'] ?? 0);
            }
        }

        return $this->response->setJSON($vars);
    }

    /**
     * Save or update an operator record.
     *
     * @return ResponseInterface JSON response indicating success or failure.
     * @throws ReflectionException
     */
    public function save(): ResponseInterface
    {
        $id = (int)($this->request->getPost('id') ?? 0);
        if ($id > 0 && $this->findOperator($id) === null) {
            return $this->response->setJSON([
                'id' => $id,
                'success' => false,
                'message' => 'Pengguna tidak ditemukan atau tidak dapat diubah dari menu ini.',
            ]);
        }

        $full_name = trim((string)$this->request->getPost('full_name'));
        $email = trim((string)$this->request->getPost('email'));
        $password = (string)$this->request->getPost('password');

        $validation = service('validation');
        $validation->setRules([
            'full_name' => UserModel::nameRule('Nama Lengkap'),
            'email' => [
                'label' => 'Email',
                'rules' => 'required|valid_email|max_length[255]|is_unique[users.email,id,' . $id . ']',
                'errors' => [
                    'required' => 'Email wajib diisi.',
                    'valid_email' => 'Format email tidak valid.',
                    'max_length' => 'Email maksimal 255 karakter.',
                    'is_unique' => 'Email sudah digunakan oleh pengguna lain.',
                ],
            ],
            'password' => [
                'label' => 'Kata Sandi',
                // Required for a new operator; optional (keep current) when editing.
                'rules' => ($id > 0 ? 'permit_empty' : 'required') . '|min_length[8]|max_length[255]',
                'errors' => [
                    'required' => 'Kata Sandi wajib diisi untuk pengguna baru.',
                    'min_length' => 'Kata Sandi minimal 8 karakter.',
                    'max_length' => 'Kata Sandi maksimal 255 karakter.',
                ],
            ],
        ]);

        $input = [
            'full_name' => $full_name,
            'email' => $email,
            'password' => $password,
        ];
        if (!$validation->run($input)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $validation->getErrors(),
            ]);
        }

        $model = new OperatorModel();
        $actor = (int)session()->get('actor');
        $unique_id = 'user:' . mb_strtolower($email);

        $collision = $model->where('unique_id', $unique_id)->where('id !=', $id)->first();
        if ($collision) {
            return $this->response->setJSON([
                'id' => 0,
                'success' => false,
                'message' => 'Email sudah digunakan oleh pengguna lain.',
            ]);
        }

        $data = [
            'unique_id' => $unique_id,
            'full_name' => $full_name,
            'email' => $email,
        ];
        if ($password !== '') {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        if ($id > 0) {
            $data['updated_by'] = $actor;
            $model->update($id, $data);
            if ($password !== '') {
                // A new password also revokes the operator's remember-me token, so an old cookie stops working.
                new Remember()->revokeUserToken($id);
            }
            return $this->response->setJSON([
                'id' => $id,
                'success' => true,
                'message' => 'Data pengguna berhasil diperbarui.',
            ]);
        }

        $data['is_operator'] = 1;
        $data['created_by'] = $actor;
        $insertId = $model->insert($data);
        return $this->response->setJSON([
            'id' => $insertId ? (int)$insertId : 0,
            'success' => (bool)$insertId,
            'message' => $insertId ? 'Pengguna berhasil ditambahkan.' : 'Terjadi kesalahan saat menyimpan data.',
        ]);
    }

    /**
     * Soft-delete an operator account.
     *
     * The logged-in administrator, other administrators and applicants cannot be deleted here.
     *
     * @return ResponseInterface JSON response indicating success or failure.
     */
    public function delete(): ResponseInterface
    {
        if ($this->findOperator($this->id) === null) {
            return $this->refused();
        }

        db_connect()->table($this->table)
            ->where('id', $this->id)
            ->where('is_operator', 1)
            ->update([
                'is_deleted' => 1,
                'deleted_by' => (int)session()->get('actor'),
                'deleted_at' => date('Y-m-d H:i:s'),
            ]);

        return $this->response->setJSON([
            'id' => $this->id,
            'success' => true,
            'message' => 'Pengguna berhasil dihapus.',
        ]);
    }

    /**
     * Restore a soft-deleted operator account.
     *
     * @return ResponseInterface JSON response indicating success or failure.
     */
    public function restore(): ResponseInterface
    {
        if ($this->findOperator($this->id) === null) {
            return $this->refused();
        }

        db_connect()->table($this->table)
            ->where('id', $this->id)
            ->where('is_operator', 1)
            ->update([
                'is_deleted' => 0,
                'deleted_by' => 0,
                'deleted_at' => null,
                'restored_by' => (int)session()->get('actor'),
                'restored_at' => date('Y-m-d H:i:s'),
            ]);

        return $this->response->setJSON([
            'id' => $this->id,
            'success' => true,
            'message' => 'Pengguna berhasil dipulihkan.',
        ]);
    }

    /**
     * Find an operator-only account (not an administrator, not an applicant,
     * and not the logged-in user) by ID.
     *
     * @param int $id The user ID.
     * @return object|null The user row (id, is_deleted), or null when it may not be managed here.
     */
    private function findOperator(int $id): ?object
    {
        if ($id <= 0 || $id === (int)session()->get('actor')) {
            return null;
        }

        $row = db_connect()->table($this->table)
            ->select('id, is_deleted')
            ->where('id', $id)
            ->where('is_operator', 1)
            ->where('is_admin', 0)
            ->where('is_student', 0)
            ->get()
            ->getRow();

        return $row ?: null;
    }

    /**
     * JSON response for a request that targets an account that may not be managed here.
     *
     * @return ResponseInterface
     */
    private function refused(): ResponseInterface
    {
        return $this->response->setJSON([
            'id' => $this->id,
            'success' => false,
            'message' => 'Pengguna tidak ditemukan atau tidak dapat diubah dari menu ini.',
        ]);
    }
}
