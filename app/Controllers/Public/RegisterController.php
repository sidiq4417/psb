<?php

declare(strict_types=1);

namespace App\Controllers\Public;

use App\Controllers\BaseController;
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
            $validation = service('validation');
            $validation->setRules([
                'full_name' => ['label' => 'Nama Lengkap', 'rules' => 'required|min_length[3]|max_length[150]'],
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

            $input = $this->request->getPost();
            if (!$validation->run((array) $input)) {
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

            if ($insert_id > 0) {
                $db->table('users')->where('id', $insert_id)->update(['created_by' => $insert_id]);
                $this->sendAccountVerificationLink($data->full_name, $data->email, $insert_id);
            }

            return $this->response->setJSON([
                'id' => $insert_id,
                'success' => $insert_id > 0,
                'message' => $insert_id > 0 ? 'Saved successfully.' : 'Error occurred.'
            ]);
        }

        return $this->response->setJSON([]);
    }

    /**
     * Send Account Verification Link via email.
     *
     * @param string $full_name Full name of the user.
     * @param string $mailTo Recipient email address.
     * @param int $registration_id User registration ID.
     * @return void
     */
    private function sendAccountVerificationLink(string $full_name = '', string $mailTo = '', int $registration_id = 0): void
    {
        $db = Database::connect();
        $setting = $db->table('application_settings')->get()->getRow();
        if (!$setting) {
            log_message('error', 'Register: settings not found.');
            return;
        }

        $school_name = $setting->institution_name ?? 'University';
        $email = Services::email();
        $config = [
            'protocol' => 'smtp',
            'SMTPHost' => $setting->smtp_host,
            'SMTPUser' => $setting->smtp_user,
            'SMTPPass' => $setting->smtp_pass,
            'SMTPPort' => (int) $setting->smtp_port,
            'mailType' => 'html',
            'charset' => 'UTF-8',
            'wordWrap' => true,
            'SMTPCrypto' => 'tls',
        ];
        $email->initialize($config);
        $email->setFrom($setting->email, 'SPMB ' . mb_strtoupper($school_name));
        if (ENVIRONMENT === 'development') {
            $mailTo = 'rector.indonesia@gmail.com';
        }
        $email->setTo($mailTo);

        $encrypter = Services::encrypter();
        $encrypted = bin2hex($encrypter->encrypt((string) $registration_id));

        $data = [
            'full_name' => $full_name,
            'email' => $mailTo,
            'school_name' => $school_name,
            'verification_link' => site_url('account-verification/' . $encrypted),
        ];

        $message = view('emails/account-verification', $data);
        $email->setSubject('Aktivasi Akun Penerimaan Murid Baru ' . $school_name);
        $email->setMessage($message);

        if (!$email->send()) {
            log_message('error', 'Register: email failed to send. ' . $email->printDebugger(['headers', 'subject']));
        }
    }
}
