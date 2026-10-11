<?php

declare(strict_types=1);

namespace App\Controllers\Public;

use App\Libraries\Remember;
use App\Models\AdmissionPeriodModel;
use App\Models\UserModel;
use App\Controllers\BaseController;
use App\Services\PasswordService;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Database;
use Config\Setting;

/**
 * Class ResetPasswordController
 *
 * Handles the password reset workflow, including token validation and password update.
 *
 * @package App\Controllers\Public
 */
class ResetPasswordController extends BaseController
{
    /**
     * Display the reset password form.
     *
     * @param string $password_reset_token The token provided in the reset link.
     * @return string Rendered HTML view.
     */
    public function index(string $password_reset_token = ''): string
    {
        $user_id = $this->findUserIdByToken($password_reset_token);
        if ($user_id > 0) {
            $db = Database::connect();
            $setting = $db->table('application_settings')->get()->getRow();

            $activeAdmissionYear = new AdmissionPeriodModel()->getActiveAdmissionYear();

            $vars = [
                'title' => 'Atur Ulang Kata Sandi',
                'password_reset_token' => $password_reset_token,
                'institution_name' => $setting->institution_name ?? 'PSB Online',
                'tagline' => $setting->tagline ?? 'Penerimaan Santri Baru (PSB) Online',
                'logo' => $setting->logo ?? 'logo.png',
                'favicon' => $setting->favicon ?? 'favicon.ico',
                'admission_year' => $activeAdmissionYear,
                'registration_terms' => !empty($setting->registration_terms) ? site_url('registration-terms/' . $setting->registration_terms) : null,
            ];

            return view('public/password-recovery', $vars);
        }

        $vars['title'] = 'Tautan Tidak Valid';
        $vars['error_code'] = '404 Not Found';
        $vars['error_message'] = 'Tautan pemulihan kata sandi tidak valid, sudah kedaluwarsa (berlaku 60 menit), atau sudah pernah digunakan. Silakan ajukan permintaan atur ulang kata sandi yang baru.';
        return view('public/unauthorized', $vars);
    }

    /**
     * Verify and process the password reset submission.
     *
     * The token stays valid (until it expires) when the new password is rejected,
     * so the user can correct it; it is cleared once the password is changed.
     *
     * @param string $password_reset_token The token provided in the reset link.
     * @return ResponseInterface JSON response indicating success or failure.
     */
    public function verify(string $password_reset_token = ''): ResponseInterface
    {
        $password = $this->request->getPost('password');
        $password_confirm = $this->request->getPost('password_confirm');
        $password = is_string($password) ? $password : '';
        $password_confirm = is_string($password_confirm) ? $password_confirm : '';

        $user_id = $this->findUserIdByToken($password_reset_token);
        if ($user_id === 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tautan pemulihan kata sandi tidak valid atau sudah kedaluwarsa. Silakan ajukan permintaan atur ulang kata sandi yang baru.'
            ]);
        }

        if ($password !== $password_confirm) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => [
                    'password_confirm' => 'Konfirmasi kata sandi tidak cocok.'
                ]
            ]);
        }

        $passwordService = new PasswordService();
        if (!$passwordService->isValid($password)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => [
                    'password' => 'Kata sandi minimal ' . Setting::MIN_PASSWORD_LENGTH . ' karakter dan harus mengandung huruf besar, huruf kecil, angka, dan simbol.'
                ]
            ]);
        }

        $success = $this->updatePassword($user_id, $password);
        if ($success) {
            // A new password ends every "remember me" login of the account.
            new Remember()->deleteUserToken($user_id);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Kata sandi berhasil diperbarui. Silakan masuk dengan kata sandi baru.',
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'errors' => [
                'password' => 'Kata sandi gagal diperbarui. Silakan coba lagi.'
            ]
        ]);
    }

    /**
     * Find the user that owns a password reset token.
     *
     * Tokens are 64 hex characters (32 random bytes); anything else is rejected
     * before the (bcrypt) comparison. Expiry (60 minutes) is enforced by the model.
     *
     * @param string $password_reset_token Token from the reset link.
     * @return int User ID, or 0 when the token is invalid or expired.
     */
    private function findUserIdByToken(string $password_reset_token): int
    {
        if (strlen($password_reset_token) !== 64 || !ctype_xdigit($password_reset_token)) {
            return 0;
        }

        return new UserModel()->findUserIdByPasswordRecoveryToken($password_reset_token);
    }

    /**
     * Update the user's password in the database.
     *
     * @param int    $user_id  User ID whose password is being reset.
     * @param string $password Plain text new password.
     * @return bool True if update succeeded, false otherwise.
     */
    private function updatePassword(int $user_id, string $password): bool
    {
        $passwordService = new PasswordService();
        $db = Database::connect();
        $db->transStart();

        $db->table('users')
            ->where('id', $user_id)
            ->update([
                'updated_by' => $user_id,
                'password' => $passwordService->hash($password),
                'password_reset_token' => null,
                'password_reset_token_request_at' => null,
            ]);

        $db->transComplete();
        return $db->transStatus() === true;
    }
}
