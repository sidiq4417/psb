<?php

declare(strict_types=1);

namespace App\Controllers\Public;

use App\Libraries\Auth;
use App\Controllers\BaseController;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Database;
use Config\Services;
use Config\Setting;
use Exception;
use Random\RandomException;

/**
 * Class LoginController
 *
 * Handles user authentication, login attempts tracking, and session management.
 *
 * @package App\Controllers\Public
 */
class LoginController extends BaseController
{
    /**
     * Authentication library instance.
     *
     * Used to verify credentials and manage login state.
     *
     * @var Auth
     */
    protected Auth $auth;

    /**
     * Database connection instance.
     *
     * Used to record and clear login attempts.
     *
     * @var BaseConnection
     */
    protected BaseConnection $db;

    /**
     * LoginController Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->auth = new Auth();
        $this->db = Database::connect();
    }

    /**
     * Handle user login.
     *
     * @return ResponseInterface Rendered view or JSON response.
     * @throws RandomException
     */
    public function index(): ResponseInterface
    {
        if ($this->request->getMethod() === 'POST') {
            $validation = service('validation');
            $validation->setRules([
                'email' => [
                    'label' => 'Email',
                    'rules' => 'required|valid_email',
                ],
                'password' => [
                    'label' => 'Kata Sandi',
                    'rules' => 'required',
                ]
            ]);
            if (!$validation->run((array)$this->request->getPost())) {
                return $this->response->setJSON([
                    'success' => false,
                    'errors' => $validation->getErrors(),
                ]);
            }

            // Verify Cloudflare Turnstile if configured
            $setting = $this->db->table('application_settings')->get()->getRow();

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

            $email = $this->request->getPost('email');
            $email = is_string($email) ? trim($email) : '';
            $password = $this->request->getPost('password');
            $password = is_string($password) ? $password : '';
            $remember_me = filter_var(
                $this->request->getPost(Setting::REMEMBER_COOKIE_NAME),
                FILTER_VALIDATE_BOOLEAN,
                FILTER_NULL_ON_FAILURE
            );
            $ip_address = $this->request->getIPAddress();

            $res = $this->auth->verify($email, $password, $remember_me, $ip_address);
            return $this->response->setJSON($res);
        }

        return $this->response->setJSON([]);
    }
}
