<?php

declare(strict_types=1);

namespace App\Controllers\Public;

use App\Models\AdmissionPeriodModel;
use App\Models\UserModel;
use App\Controllers\BaseController;
use App\Services\PasswordService;
use Config\Database;

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
     * @return string|null Rendered HTML view or null if user not found.
     */
    public function index(string $password_reset_token): ?string
    {
        $user = new UserModel();
        $user_id = $user->findUserIdByPasswordRecoveryToken($password_reset_token);
        if ($user_id > 0) {
            $db = Database::connect();
            $user_data = $db->table('users')->where('id', $user_id)->get()->getRow();
            $password_reset_token_request_at = $user_data->password_reset_token_request_at ?? '';
            if (!empty($password_reset_token_request_at) && (strtotime($password_reset_token_request_at) + 3600) < time()) {
                $vars['title'] = 'Unauthorized';
                $vars['error_code'] = '400 Bad Request';
                $vars['error_message'] = 'Link has expired, password reset link is only active for 60 minutes.';
                return view('public/unauthorized', $vars);
            }

            $setting = $db->table('application_settings')->get()->getRow();

            $activeAdmissionYear = new AdmissionPeriodModel()->getActiveAdmissionYear();

            $vars = [
                'title' => 'Atur Ulang Kata Sandi',
                'password_reset_token' => $password_reset_token,
                'institution_name' => $setting->institution_name ?? 'Nama Instansi',
                'tagline' => $setting->tagline ?? 'Portal Admisi Terpadu',
                'logo' => $setting->logo ?? 'logo.png',
                'admission_year' => $activeAdmissionYear,
            ];

            return view('public/password-recovery', $vars);
        }

        $vars['title'] = 'Tautan Tidak Valid';
        $vars['error_code'] = '404 Not Found';
        $vars['error_message'] = 'Tautan pemulihan kata sandi tidak valid atau sudah pernah digunakan sebelumnya.';
        return view('public/unauthorized', $vars);
    }

    /**
     * Verify and process the password reset submission.
     *
     * @param string $password_reset_token The token provided in the reset link.
     * @return object|null JSON response object indicating success or failure.
     */
    public function verify(string $password_reset_token): ?object
    {
        $password = $this->request->getPost('password');
        $password_confirm = $this->request->getPost('password_confirm');

        $user = new UserModel();
        $user_id = $user->findUserIdByPasswordRecoveryToken($password_reset_token);
        if ($user_id === 0) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => [
                    'password' => 'Your account does not exist.'
                ]
            ]);
        }

        if ($password !== $password_confirm) {
            $this->invalidateResetToken($user_id);
            return $this->response->setJSON([
                'success' => false,
                'errors' => [
                    'password' => 'Your password and confirmation password does not match.'
                ]
            ]);
        }

        $passwordService = new PasswordService();
        if (!$passwordService->isValid($password)) {
            $this->invalidateResetToken($user_id);
            return $this->response->setJSON([
                'success' => false,
                'errors' => [
                    'password' => 'Password does not meet complexity requirements.'
                ]
            ]);
        }

        $success = $this->updatePassword($user_id, $password);
        if ($success) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Your password changed successfully.',
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'errors' => [
                'password' => 'Your password failed to change.'
            ]
        ]);
    }

    /**
     * Invalidate the password reset token for a user.
     *
     * @param int $user_id User ID whose token should be invalidated.
     * @return void
     */
    private function invalidateResetToken(int $user_id): void
    {
        $db = Database::connect();
        $db->table('users')
            ->where('id', $user_id)
            ->update([
                'password_reset_token' => null,
                'password_reset_token_request_at' => null,
            ]);
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
