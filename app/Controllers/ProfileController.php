<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Libraries\Remember;
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
     * The current password is required whenever the email or the password changes.
     *
     * @return ResponseInterface JSON response indicating success or failure.
     * @throws ReflectionException
     */
    public function save(): ResponseInterface
    {
        if ($this->id <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => ['auth' => 'Sesi Anda telah berakhir. Silakan masuk kembali.']
            ]);
        }

        $userModel = new UserModel();
        $existing = $userModel->find($this->id);
        if (!$existing) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => ['user' => 'Pengguna tidak ditemukan.']
            ]);
        }

        $input = $this->request->getJSON(true) ?? [];
        if (!is_array($input)) {
            $input = [];
        }
        foreach (['full_name', 'email', 'current_password', 'password', 'password_confirm'] as $field) {
            $input[$field] = is_string($input[$field] ?? null) ? $input[$field] : '';
        }
        $input['full_name'] = trim($input['full_name']);
        $input['email'] = trim($input['email']);

        $full_name = $input['full_name'];
        $email = $input['email'];

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

        if (empty($email)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => ['email' => 'Email harus diisi']
            ]);
        }

        if ($userModel->emailExistsForOtherUser($email, $this->id)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => ['email' => 'Email sudah digunakan oleh pengguna lain']
            ]);
        }

        $validation = service('validation');
        $rules = [
            'email' => ['label' => 'Email', 'rules' => 'required|valid_email|max_length[200]'],
        ];

        // The name is locked once the registration has been confirmed, so it is neither validated nor saved then.
        if (!$is_submitted) {
            $rules['full_name'] = [
                'label' => 'Nama Lengkap',
                'rules' => ['required', 'min_length[3]', 'max_length[150]', 'regex_match[/^[\p{L}\p{M}\s.\'`-]+$/u]'],
                'errors' => [
                    'required' => 'Nama Lengkap wajib diisi.',
                    'min_length' => 'Nama Lengkap minimal 3 karakter.',
                    'max_length' => 'Nama Lengkap maksimal 150 karakter.',
                    'regex_match' => 'Nama Lengkap hanya boleh berisi huruf, spasi, titik, apostrof, dan tanda hubung.'
                ]
            ];
        }

        $current_password = $input['current_password'];
        $password = $input['password'];
        $password_confirm = $input['password_confirm'];

        $changeEmail = mb_strtolower($email) !== mb_strtolower((string) ($existing->email ?? ''));
        $changePassword = $password !== '' || $password_confirm !== '';

        if ($changeEmail || $changePassword) {
            if ($current_password === '') {
                return $this->response->setJSON([
                    'success' => false,
                    'errors' => ['current_password' => 'Masukkan kata sandi saat ini untuk mengubah email atau kata sandi.']
                ]);
            }

            if (strlen($current_password) > Setting::MAX_PASSWORD_SIZE_BYTES
                || !password_verify($current_password, (string) ($existing->password ?? ''))
            ) {
                return $this->response->setJSON([
                    'success' => false,
                    'errors' => ['current_password' => 'Kata sandi saat ini tidak sesuai']
                ]);
            }
        }

        if ($changePassword) {
            $rules['password'] = ['label' => 'Kata Sandi Baru', 'rules' => 'required|min_length[' . Setting::MIN_PASSWORD_LENGTH . ']|max_length[255]'];
            $rules['password_confirm'] = [
                'label' => 'Ulangi Kata Sandi Baru',
                'rules' => 'required|matches[password]',
                'errors' => ['matches' => 'Ulangi kata sandi baru tidak cocok dengan kata sandi baru.']
            ];
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

        // Keep the name/email shown in the layout in sync.
        if (!$is_submitted) {
            session()->set('full_name', $full_name);
        }
        session()->set('email', $email);

        if ($changePassword) {
            // A new password ends every "remember me" login of the account.
            new Remember()->deleteUserToken($this->id);
        }

        return $this->response->setJSON([
            'id' => $this->id,
            'success' => true,
            'message' => 'Perubahan berhasil disimpan.',
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
