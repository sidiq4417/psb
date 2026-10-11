<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\RegistrationModel;
use App\Models\UserModel;
use App\Traits\DataTrait;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Database;
use Config\Setting;
use ReflectionException;

class ProfileController extends BaseController
{
    protected int $id = 0;

    use DataTrait;

    /**
     * ProfileController Constructor.
     *
     * Initializes the controller by retrieving the current
     * logged-in user's ID from the session.
     *
     * @return void
     */
    public function __construct()
    {
        $this->id = (int) session()->get('actor');
    }

    /**
     * Render the profile page for the current user.
     *
     * @return string Rendered HTML view.
     */
    public function index(): string
    {
        $userModel = new UserModel();
        $data = $userModel->find($this->id);

        if ($this->isAdminOrOperator()) {
            $vars['title'] = 'Profil Saya';
            $vars['breadcrumb'] = 'Profil';
            $vars['full_name'] = $data->full_name ?? '';
            $vars['email'] = $data->email ?? '';
            $vars['password_hash'] = $data->password ?? '';
            return view('profile', $vars);
        }

        $is_submitted = false;
        $rows = new RegistrationModel()->findByStudentId($this->id)->getResult();
        if (!empty($rows)) {
            $db = Database::connect();
            $reg = $db->table('registrations')->where('id', (int) $rows[0]->id)->get()->getRow();
            $is_submitted = (bool) ($reg->submitted ?? false);
        }

        $vars['title'] = 'Akun Saya';
        $vars['full_name'] = $data->full_name ?? '';
        $vars['email'] = $data->email ?? '';
        $vars['is_submitted'] = $is_submitted;
        return view('students/account', $vars);
    }

    /**
     * Update the current user's profile information.
     *
     * @return ResponseInterface JSON response indicating success or failure.
     * @throws ReflectionException
     */
    public function save(): ResponseInterface
    {
        if ($this->id <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => ['auth' => 'Unauthorized access.']
            ]);
        }

        $userModel = new UserModel();
        $existing = $userModel->find($this->id);
        if (!$existing) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => ['user' => 'User not found.']
            ]);
        }

        $input = $this->request->getJSON(true) ?? [];

        $full_name = trim($input['full_name'] ?? '');
        $email = trim($input['email'] ?? '');

        $is_student = (bool) (session()->get('is_student') ?? false);

        $is_submitted = false;
        if ($is_student) {
            $rows = new RegistrationModel()->findByStudentId($this->id)->getResult();
            if (!empty($rows)) {
                $db = Database::connect();
                $reg = $db->table('registrations')->where('id', (int) $rows[0]->id)->get()->getRow();
                $is_submitted = (bool) ($reg->submitted ?? false);
            }
        }

        if ($userModel->emailExistsForOtherUser($email, $this->id)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => ['email' => 'Email sudah digunakan oleh pengguna lain']
            ]);
        }

        if (empty($email)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => ['email' => 'Email harus diisi']
            ]);
        }

        $validation = service('validation');
        $rules = [
            'full_name' => ['label' => 'Nama Lengkap', 'rules' => 'required|max_length[200]'],
            'email' => ['label' => 'Email', 'rules' => 'required|valid_email|max_length[200]'],
        ];

        $current_password = $input['current_password'] ?? '';
        $password = $input['password'] ?? '';
        $password_confirm = $input['password_confirm'] ?? '';

        $changePassword = !empty($current_password) || !empty($password) || !empty($password_confirm);

        if ($changePassword) {
            $password_hash = $existing->password ?? '';
            if (!password_verify($current_password, $password_hash)) {
                return $this->response->setJSON([
                    'success' => false,
                    'errors' => ['current_password' => 'Kata sandi saat ini tidak sesuai']
                ]);
            }

            $rules['current_password'] = ['label' => 'Kata Sandi Saat Ini', 'rules' => 'required'];
            $rules['password'] = ['label' => 'Kata Sandi Baru', 'rules' => 'required|min_length[8]'];
            $rules['password_confirm'] = ['label' => 'Ulangi Kata Sandi Baru', 'rules' => 'required|matches[password]'];
        }

        $validation->setRules($rules);

        if (!$validation->run($input)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $validation->getErrors(),
            ]);
        }

        $updateData = [];
        if (!$is_submitted) {
            $updateData['full_name'] = $full_name;
        }
        $updateData['email'] = $email;

        if ($changePassword) {
            $updateData['password'] = password_hash(
                $password,
                PASSWORD_BCRYPT,
                ['cost' => Setting::PASSWORD_COST ?? 10]
            );
        }

        $updateData['updated_by'] = (int) session()->get('actor');
        $userModel->update($this->id, $updateData);

        return $this->response->setJSON([
            'id' => $this->id,
            'success' => true,
            'message' => 'Updated successfully.',
        ]);
    }

    /**
     * Check if the current user is an admin or operator.
     *
     * @return bool True if admin or operator, false otherwise.
     */
    private function isAdminOrOperator(): bool
    {
        return session()->get('is_admin') || session()->get('is_operator');
    }
}
