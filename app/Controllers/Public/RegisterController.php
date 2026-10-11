<?php

declare(strict_types=1);

namespace App\Controllers\Public;

use App\Controllers\BaseController;
use App\Services\AccountVerificationService;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Database;
use Config\Services;
use Exception;
use stdClass;

/**
 * Class RegisterController
 *
 * Handles the user registration process and account activation notification.
 *
 * @package App\Controllers\Public
 */
class RegisterController extends BaseController
{
    /**
     * Handle user registration.
     *
     * @return string|ResponseInterface Rendered view or JSON response.
     */
    public function index(): string|ResponseInterface
    {
        if ($this->request->getMethod() === 'POST') {
            $input = (array) $this->request->getPost();
            foreach (['full_name', 'email', 'mobile_phone'] as $field) {
                if (isset($input[$field]) && is_string($input[$field])) {
                    $input[$field] = trim($input[$field]);
                }
            }

            $validation = service('validation');
            $validation->setRules([
                'full_name' => [
                    'label' => 'Nama Lengkap',
                    'rules' => ['required', 'min_length[3]', 'max_length[150]', 'regex_match[/^[\p{L}\p{M}\s.\'`-]+$/u]'],
                    'errors' => [
                        'required' => 'Nama Lengkap wajib diisi.',
                        'min_length' => 'Nama Lengkap minimal 3 karakter.',
                        'max_length' => 'Nama Lengkap maksimal 150 karakter.',
                        'regex_match' => 'Nama Lengkap hanya boleh berisi huruf, spasi, titik, apostrof, dan tanda hubung.'
                    ]
                ],
                'email' => [
                    'label' => 'Email',
                    'rules' => 'required|valid_email|is_unique[users.email]',
                    'errors' => [
                        'is_unique' => '{field} sudah terdaftar. Silakan gunakan email lain atau gunakan fitur lupa kata sandi.'
                    ]
                ],
                'mobile_phone' => ['label' => 'Nomor Handphone', 'rules' => 'required|min_length[10]|max_length[13]'],
                'password' => ['label' => 'Kata Sandi', 'rules' => 'required|min_length[8]|max_length[255]']
            ]);

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

            $data = new stdClass();
            $data->unique_id = $input['email'];
            $data->full_name = $input['full_name'];
            $data->email = $input['email'];
            $data->mobile_phone = $input['mobile_phone'];
            $data->password = password_hash($input['password'], PASSWORD_DEFAULT);
            $data->is_student = true;

            $db->table('users')->insert((array)$data);
            $insert_id = (int)$db->insertID();

            if ($insert_id <= 0) {
                return $this->response->setJSON([
                    'id' => 0,
                    'success' => false,
                    'message' => 'Pendaftaran gagal disimpan. Silakan coba lagi.'
                ]);
            }

            $db->table('users')->where('id', $insert_id)->update(['created_by' => $insert_id]);
            $email_sent = new AccountVerificationService()->send($insert_id, $data->full_name, $data->email);

            return $this->response->setJSON([
                'id' => $insert_id,
                'success' => true,
                'email_sent' => $email_sent,
                'message' => $email_sent
                    ? 'Pendaftaran berhasil. Silakan buka email Anda (periksa juga folder spam) dan klik tautan verifikasi untuk mengaktifkan akun.'
                    : 'Akun berhasil dibuat, tetapi email verifikasi gagal dikirim. Silakan hubungi panitia PSB.'
            ]);
        }

        return $this->response->setJSON([]);
    }
}
