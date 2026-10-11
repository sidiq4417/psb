<?php

declare(strict_types=1);

namespace App\Controllers\Administrator;

use App\Controllers\BaseController;
use App\Libraries\Auth;
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
     * Setting columns that hold an uploaded file, with the label shown in messages.
     *
     * @var array<string, string>
     */
    private const array UPLOAD_FIELDS = [
        'logo' => 'Logo',
        'favicon' => 'Favicon',
        'payment_method' => 'Panduan Pembayaran',
        'registration_terms' => 'Syarat & Ketentuan',
    ];

    /**
     * Validation rules for each uploadable setting file.
     *
     * Logo and favicon are small images; the guides are PDF documents.
     *
     * @var array<string, string>
     */
    private const array UPLOAD_RULES = [
        'logo' => 'uploaded[file]|ext_in[file,png,jpg,jpeg,gif]|mime_in[file,image/png,image/jpeg,image/gif]|max_size[file,1024]',
        'favicon' => 'uploaded[file]|ext_in[file,ico,png,gif]|mime_in[file,image/x-icon,image/vnd.microsoft.icon,image/png,image/gif]|max_size[file,512]',
        'payment_method' => 'uploaded[file]|ext_in[file,pdf]|mime_in[file,application/pdf,application/x-pdf]|max_size[file,10240]',
        'registration_terms' => 'uploaded[file]|ext_in[file,pdf]|mime_in[file,application/pdf,application/x-pdf]|max_size[file,10240]',
    ];

    /**
     * Secret columns that are never sent to the browser and are only
     * overwritten when a new non-empty value is submitted.
     *
     * @var list<string>
     */
    private const array SECRET_FIELDS = ['smtp_pass', 'turnstile_secret_key'];

    /**
     * Display the application settings page.
     *
     * @return string Rendered HTML view.
     */
    public function index(): string
    {
        $db = db_connect();
        $dm = $db->table($this->table)->where('id', $this->settings_id)->get()->getRow();

        // Secrets stay on the server; the view only learns whether one is stored.
        $stored_secrets = [];
        foreach (self::SECRET_FIELDS as $field) {
            $stored_secrets[$field] = is_object($dm) && trim((string) ($dm->$field ?? '')) !== '';
            if (is_object($dm)) {
                unset($dm->$field);
            }
        }

        $vars = [
            'title' => 'Pengaturan Sistem',
            'row' => $dm,
            'stored_secrets' => $stored_secrets,
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
        // Phone numbers are optional (a fresh install has none); when given, they may contain
        // digits with the usual separators, e.g. "(0271) 123-456" or "+62 812 3456 7890".
        $phone_rule = static fn(string $label): array => [
            'label' => $label,
            'rules' => ['permit_empty', 'max_length[50]', 'regex_match[/^(?=(?:\D*\d){5,15}\D*$)\+?[0-9 ().-]+$/D]'],
            'errors' => [
                'regex_match' => $label . ' hanya boleh berisi 5-15 digit angka (boleh diawali +, dengan spasi, titik, tanda hubung, atau tanda kurung).',
            ],
        ];

        $validation = service('validation');
        $validation->setRules([
            'institution_name' => ['label' => 'Nama Instansi', 'rules' => 'required|max_length[255]'],
            'tagline' => ['label' => 'Tagline', 'rules' => 'permit_empty|max_length[255]'],
            'address' => ['label' => 'Alamat', 'rules' => 'required|max_length[1000]'],
            'email' => ['label' => 'Email', 'rules' => 'required|valid_email|max_length[255]'],
            'telephone' => $phone_rule('Telepon Kantor'),
            'mobile_phone' => $phone_rule('Handphone / WhatsApp'),
            'timezone' => ['label' => 'Zona Waktu', 'rules' => 'required|timezone'],
            'smtp_host' => ['label' => 'SMTP Host', 'rules' => 'permit_empty|max_length[255]'],
            'smtp_user' => ['label' => 'SMTP Username', 'rules' => 'permit_empty|max_length[255]'],
            'smtp_pass' => ['label' => 'SMTP Password', 'rules' => 'permit_empty|max_length[255]'],
            'smtp_port' => ['label' => 'SMTP Port', 'rules' => 'permit_empty|is_natural_no_zero|less_than_equal_to[65535]'],
            'turnstile_site_key' => ['label' => 'Site Key', 'rules' => 'permit_empty|max_length[255]'],
            'turnstile_secret_key' => ['label' => 'Secret Key', 'rules' => 'permit_empty|max_length[255]'],
            'max_registrations' => ['label' => 'Maksimal Jumlah Pendaftaran', 'rules' => 'permit_empty|is_natural_no_zero|less_than_equal_to[50]'],
        ]);

        $post = (array) $this->request->getPost();
        foreach (['telephone', 'mobile_phone'] as $field) {
            if (is_string($post[$field] ?? null)) {
                $post[$field] = trim($post[$field]);
            }
        }

        if (!$validation->run($post)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $validation->getErrors(),
            ]);
        }

        $db = db_connect();

        $data = [
            'institution_name' => trim((string) $this->request->getPost('institution_name')),
            'tagline' => trim((string) $this->request->getPost('tagline')),
            'address' => trim((string) $this->request->getPost('address')),
            'email' => trim((string) $this->request->getPost('email')),
            'telephone' => trim((string) $this->request->getPost('telephone')),
            'mobile_phone' => trim((string) $this->request->getPost('mobile_phone')),
            'timezone' => (string) $this->request->getPost('timezone'),
            'smtp_host' => trim((string) $this->request->getPost('smtp_host')),
            'smtp_user' => trim((string) $this->request->getPost('smtp_user')),
            'smtp_port' => trim((string) $this->request->getPost('smtp_port')),
            'turnstile_site_key' => trim((string) $this->request->getPost('turnstile_site_key')),
            'allow_multiple_registration' => filter_var($this->request->getPost('allow_multiple_registration'), FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
            'max_registrations' => (int) $this->request->getPost('max_registrations') ?: 3,
        ];

        // Optional phone numbers are stored as NULL when left empty (as in a fresh install).
        foreach (['telephone', 'mobile_phone'] as $field) {
            if ($data[$field] === '') {
                $data[$field] = null;
            }
        }

        // Secrets are write-only: an empty field keeps the stored value,
        // the "clear_*" checkbox removes it.
        foreach (self::SECRET_FIELDS as $field) {
            if (filter_var($this->request->getPost('clear_' . $field), FILTER_VALIDATE_BOOLEAN)) {
                $data[$field] = null;
                continue;
            }
            $value = (string) $this->request->getPost($field);
            if (trim($value) !== '') {
                $data[$field] = $value;
            }
        }

        // Login and registration verify Turnstile whenever a secret key is stored, but the
        // widget is only shown when a site key exists: a secret alone would lock everyone out.
        $stored = $db->table($this->table)->select('turnstile_secret_key')->where('id', $this->settings_id)->get()->getRow();
        $secret_after = array_key_exists('turnstile_secret_key', $data)
            ? (string) $data['turnstile_secret_key']
            : (string) ($stored->turnstile_secret_key ?? '');
        if (trim($secret_after) !== '' && $data['turnstile_site_key'] === '') {
            return $this->response->setJSON([
                'success' => false,
                'errors' => ['turnstile_site_key' => 'Site Key wajib diisi jika Secret Key Turnstile digunakan.'],
            ]);
        }

        $db->table($this->table)->where('id', $this->settings_id)->update($data);

        // Refresh the values cached in the session (page title, footer, sidebar logo, favicon).
        Auth::cacheBranding($db->table($this->table)->where('id', $this->settings_id)->get()->getRow());

        return $this->response->setJSON([
            'id' => $this->settings_id,
            'success' => true,
            'message' => 'Pengaturan berhasil disimpan.',
        ]);
    }

    /**
     * Upload a system file: logo, favicon, payment method or registration terms.
     *
     * @return ResponseInterface JSON response indicating success or failure.
     */
    public function upload(): ResponseInterface
    {
        $document_name = (string) $this->request->getPost('document_name');
        if (!array_key_exists($document_name, self::UPLOAD_FIELDS)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'errors' => ['file' => 'Jenis berkas tidak valid.'],
            ]);
        }

        $validated = $this->validate([
            'file' => [
                'label' => self::UPLOAD_FIELDS[$document_name],
                'rules' => self::UPLOAD_RULES[$document_name],
            ],
        ]);

        if (!$validated) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors(),
            ]);
        }

        $file = $this->request->getFile('file');
        if ($file === null || !$file->isValid() || $file->hasMoved()) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => ['file' => $file?->getErrorString() ?? 'Berkas tidak ditemukan.'],
            ]);
        }

        $upload_path = public_upload_path();
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0755, true);
        }

        $random_name = $file->getRandomName();
        $file->move($upload_path, $random_name);

        $db = db_connect();
        $old = $db->table($this->table)->select($document_name)->where('id', $this->settings_id)->get()->getRow();
        $old_file_name = basename((string) ($old->{$document_name} ?? ''));

        $db->table($this->table)->where('id', $this->settings_id)->update([$document_name => $random_name]);

        // Only ever delete the previous file of this setting, inside the public uploads folder.
        if (
            $old_file_name !== '' && $old_file_name !== '.' && $old_file_name !== '..'
            && $old_file_name !== $random_name && !str_starts_with($old_file_name, '.')
            && $old_file_name !== 'index.html' && is_file($upload_path . $old_file_name)
        ) {
            @unlink($upload_path . $old_file_name);
        }

        // Keep the session copy used by the layout and sidebar in sync.
        if ($document_name === 'favicon' || $document_name === 'logo') {
            Auth::cacheBranding($db->table($this->table)->where('id', $this->settings_id)->get()->getRow());
        }

        return $this->response->setJSON([
            'success' => true,
            'file' => $random_name,
            'message' => self::UPLOAD_FIELDS[$document_name] . ' berhasil diunggah.',
        ]);
    }

    /**
     * Download one of the uploaded setting files (logo, favicon, payment method or registration terms).
     *
     * @param string $file_name The name of the file to download.
     * @return ResponseInterface The download response.
     * @throws PageNotFoundException When the file is not a current setting file or does not exist.
     */
    public function download(string $file_name = ''): ResponseInterface
    {
        $safe_name = basename($file_name);
        $row = db_connect()->table($this->table)
            ->select(implode(', ', array_keys(self::UPLOAD_FIELDS)))
            ->where('id', $this->settings_id)
            ->get()
            ->getRow();

        $field = null;
        foreach (array_keys(self::UPLOAD_FIELDS) as $column) {
            $value = basename((string) ($row->$column ?? ''));
            if ($value !== '' && $value === $safe_name) {
                $field = $column;
                break;
            }
        }

        $file_path = public_upload_path() . $safe_name;
        if ($field === null || !is_file($file_path)) {
            throw PageNotFoundException::forPageNotFound();
        }

        $response = $this->response->download($file_path, null, true)->setFileName($safe_name);

        // Images are shown in the browser; documents are downloaded.
        return in_array($field, ['logo', 'favicon'], true) ? $response->inline() : $response;
    }
}
