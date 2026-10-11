<?php

declare(strict_types=1);

namespace App\Controllers\Public;

use App\Models\ApplicationSettingModel;
use App\Controllers\BaseController;
use App\Services\MailService;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Database;
use Config\Services;
use Exception;
use Random\RandomException;

/**
 * Class ForgotPasswordController
 *
 * Handles the password reset request flow, including token generation and email notification.
 *
 * @package App\Controllers\Public
 */
class ForgotPasswordController extends BaseController
{
    /**
     * Handle the password reset link request.
     *
     * @return ResponseInterface|null JSON response on POST request or redirect on GET.
     * @throws RandomException
     */
    public function sendPasswordResetLink(): ?ResponseInterface
    {
        if ($this->request->getMethod() === 'GET') {
            return redirect()->to(site_url());
        }

        $validation = service('validation');
        $validation->setRules([
            'email' => [
                'label' => 'Email',
                'rules' => 'required|valid_email',
            ]
        ]);

        $input = $this->request->getPost();

        if (!$validation->run($input)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $validation->getErrors(),
            ]);
        }

        // Verify Cloudflare Turnstile if configured
        $db = Database::connect();
        $setting = $db->table('application_settings')->get()->getRow();

        if (!empty($setting->turnstile_secret_key)) {
            $turnstileResponse = $this->request->getPost('cf-turnstile-response');
            if (empty($turnstileResponse)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validasi keamanan gagal. Harap refresh halaman dan coba lagi.'
                ]);
            }

            $verifyUrl = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
            $client = Services::curlrequest();
            try {
                $response = $client->post($verifyUrl, [
                    'form_params' => [
                        'secret' => $setting->turnstile_secret_key,
                        'response' => $turnstileResponse,
                        'remoteip' => $this->request->getIPAddress()
                    ]
                ]);

                $result = json_decode($response->getBody(), true);
                if (empty($result['success'])) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Sistem mendeteksi aktivitas mencurigakan. Validasi keamanan gagal.'
                    ]);
                }
            } catch (Exception) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal terhubung ke server validasi keamanan. Silakan coba lagi.'
                ]);
            }
        }

        $email = trim((string) ($input['email'] ?? ''));

        // Looked up here (not via UserModel::findUserIdByEmail, which returns the ID as a string
        // and fails its int return type) so a registered email cannot be told apart by an error.
        $row = Database::connect()->table('users')
            ->select('id')
            ->where('is_deleted', false)
            ->where('email', $email)
            ->get()
            ->getRow();
        $user_id = (int) ($row->id ?? 0);

        if ($user_id > 0) {
            // Generate secure random token
            $token = bin2hex(random_bytes(32));
            $affectedRows = $this->updatePasswordResetToken($user_id, $token);
            if ($affectedRows > 0) {
                $this->sendResetPasswordLink($user_id, $token);
            }
        }

        // Return a generic success message to prevent user enumeration
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Instruksi pemulihan kata sandi telah dikirim ke email Anda jika terdaftar.',
        ]);
    }

    /**
     * Send Reset Password Link via email.
     *
     * @param int $user_id The user identifier.
     * @param string $token The reset token (plain text; only its hash is stored).
     * @return void
     */
    private function sendResetPasswordLink(int $user_id, string $token): void
    {
        $db = Database::connect();
        $user_data = $db->table('users')->where('id', $user_id)->get()->getRow();
        $setting = new ApplicationSettingModel()->getSetting();

        if (!$user_data) {
            log_message('error', 'ForgotPassword: user not found.');
            return;
        }

        $data = [
            'full_name' => $user_data->full_name,
            'email' => $user_data->email,
            'school_name' => (string) ($setting->institution_name ?? ''),
            'reset_password_link' => site_url('reset-password/' . $token),
        ];

        $message = view('emails/reset-password', $data);
        if (!new MailService()->send((string) $user_data->email, 'Notifikasi Permintaan Pengaturan Ulang Kata Sandi', $message)) {
            log_message('error', 'ForgotPassword: reset email could not be sent for user ' . $user_id . '.');
        }
    }

    /**
     * Update Password Reset Token in database.
     *
     * @param int $user_id The user identifier.
     * @param string $password_reset_token The token to store.
     * @return int Number of affected rows.
     */
    private function updatePasswordResetToken(int $user_id, string $password_reset_token): int
    {
        $db = Database::connect();
        $db->table('users')
            ->where('id', $user_id)
            ->update([
                'updated_by' => $user_id,
                'password_reset_token' => password_hash($password_reset_token, PASSWORD_BCRYPT),
                'password_reset_token_request_at' => date('Y-m-d H:i:s'),
            ]);

        return $db->affectedRows();
    }
}
