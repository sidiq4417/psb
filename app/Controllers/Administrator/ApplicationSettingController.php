<?php

declare(strict_types=1);

namespace App\Controllers\Administrator;

use App\Controllers\BaseController;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;
use DateTimeZone;

/**
 * Class ApplicationSettingController
 *
 * Handles the global application settings such as university profile and system configuration.
 *
 * @package App\Controllers\Administrator
 */
class ApplicationSettingController extends BaseController
{
    /**
     * Database table name.
     *
     * @var string
     */
    protected string $table = 'application_settings';

    /**
     * The application settings always reside in row ID 1.
     */
    protected int $settings_id = 1;

    /**
     * Display the application settings page.
     *
     * @return string Rendered HTML view.
     */
    public function index(): string
    {
        $db = db_connect();
        $dm = $db->table($this->table)->where('id', $this->settings_id)->get()->getRow();

        $vars = [
            'title' => 'Pengaturan Sistem',
            'row' => $dm,
            'timezones' => DateTimeZone::listIdentifiers(DateTimeZone::ASIA),
        ];

        return view('administrator/application-settings', $vars);
    }

    /**
     * Save or update application settings.
     *
     * @return ResponseInterface JSON response indicating success or failure.
     */
    public function save(): ResponseInterface
    {
        $validation = service('validation');
        $validation->setRules([
            'institution_name' => ['label' => 'Nama Instansi', 'rules' => 'required'],
            'address' => ['label' => 'Address', 'rules' => 'required'],
            'email' => ['label' => 'Email', 'rules' => 'required|valid_email'],
            'telephone' => ['label' => 'Telephone', 'rules' => 'required'],
            'mobile_phone' => ['label' => 'Mobile Phone', 'rules' => 'required'],
            'timezone' => ['label' => 'Timezone', 'rules' => 'required'],
        ]);

        if (!$validation->run($this->request->getPost())) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $validation->getErrors(),
            ]);
        }

        $db = db_connect();

        $data = [
            'institution_name' => $this->request->getPost('institution_name'),
            'tagline' => $this->request->getPost('tagline'),
            'address' => $this->request->getPost('address'),
            'email' => $this->request->getPost('email'),
            'telephone' => $this->request->getPost('telephone'),
            'mobile_phone' => $this->request->getPost('mobile_phone'),
            'timezone' => $this->request->getPost('timezone'),
            'smtp_host' => $this->request->getPost('smtp_host'),
            'smtp_user' => $this->request->getPost('smtp_user'),
            'smtp_pass' => $this->request->getPost('smtp_pass'),
            'smtp_port' => $this->request->getPost('smtp_port'),
            'turnstile_site_key' => $this->request->getPost('turnstile_site_key'),
            'turnstile_secret_key' => $this->request->getPost('turnstile_secret_key'),
            'allow_multiple_registration' => (int)(bool)$this->request->getPost('allow_multiple_registration'),
            'max_registrations' => (int)$this->request->getPost('max_registrations') ?: 3,
        ];

        $db->table($this->table)->where('id', $this->settings_id)->update($data);

        $res = (object)[
            'id' => $this->settings_id,
            'success' => true,
            'message' => 'Updated successfully.',
        ];

        if ($res->success) {
            session()->set('institution_name', $data['institution_name']);
        }

        return $this->response->setJSON($res);
    }

    /**
     * Upload a system document (e.g., payment method or registration terms).
     *
     * @return ResponseInterface JSON response indicating success or failure.
     */
    public function upload(): ResponseInterface
    {
        $document_name = $this->request->getPost('document_name');
        $file = $this->request->getFile('file');

        // Default rules: image files up to 2 MB
        $rules = 'uploaded[file]|mime_in[file,image/jpg,image/jpeg,image/gif,image/png]|max_size[file,2048]';

        // Special rules: PDF files up to 10 MB
        if ($document_name === 'payment_method' || $document_name === 'registration_terms') {
            $rules = 'uploaded[file]|ext_in[file,pdf]|mime_in[file,application/pdf,application/x-pdf]|max_size[file,10240]';
        }

        $validated = $this->validate([
            'file' => ['rules' => $rules],
        ]);

        if (!$validated) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors(),
            ]);
        }

        if ($file->isValid() && !$file->hasMoved()) {
            $uploadPath = FCPATH . 'uploads/';
            $randomName = $file->getRandomName();
            $file->move($uploadPath, $randomName);

            $db = db_connect();
            $old = $db->table($this->table)->select($document_name)->where('id', $this->settings_id)->get()->getRow();
            $old_file_name = $old->{$document_name} ?? '';

            $db->table($this->table)->where('id', $this->settings_id)->update([$document_name => $randomName]);

            if ($old_file_name && file_exists($uploadPath . $old_file_name)) {
                @unlink($uploadPath . $old_file_name);
            }

            return $this->response->setJSON([
                'success' => true,
                'file' => $randomName,
                'message' => 'Uploaded successfully.',
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'errors' => [
                'code' => 500,
                'message' => $file->getError(),
            ],
        ]);
    }

    /**
     * Download an uploaded system document (e.g., payment method or registration terms).
     *
     * @param string $file_name The name of the file to download.
     * @return ResponseInterface The download response.
     */
    public function download(string $file_name): ResponseInterface
    {
        $safe_name = basename($file_name);
        $file_path = FCPATH . 'uploads/' . $safe_name;

        if (!is_file($file_path)) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $this->response->download($file_path, null)->setFileName($safe_name);
    }
}
