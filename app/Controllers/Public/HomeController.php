<?php

declare(strict_types=1);

namespace App\Controllers\Public;

use App\Models\AdmissionPeriodModel;
use App\Models\BranchModel;
use App\Controllers\BaseController;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Class HomeController
 *
 * Handles the public home page and registration landing page.
 *
 * @package App\Controllers\Public
 */
class HomeController extends BaseController
{
    /**
     * Display the public registration page.
     *
     * @return string Rendered HTML view of the home page.
     */
    public function index(): string
    {
        $db = db_connect();
        $setting = $db->table('application_settings')->where('id', 1)->get()->getRow();

        $activeAdmissionYear = new AdmissionPeriodModel()->getActiveAdmissionYear();

        $branchModel = new BranchModel();
        $branches = $branchModel->findAll();

        return view('public/home', [
            'institution_name' => $setting->institution_name ?? 'Nama Instansi',
            'tagline' => $setting->tagline ?? 'Portal Admisi Terpadu',
            'address' => $setting->address ?? '',
            'email' => $setting->email ?? '',
            'telephone' => $setting->telephone ?? '',
            'mobile_phone' => $setting->mobile_phone ?? '',
            'timezone' => $setting->timezone ?? '',
            'logo' => $setting->logo ?? 'logo.png',
            'admission_year' => $activeAdmissionYear,
            'registration_terms' => !empty($setting->registration_terms) ? site_url('registration-terms/' . $setting->registration_terms) : null,
            'branches' => $branches,
            'favicon' => $setting->favicon ?? 'favicon.ico',
            'turnstile_site_key' => $setting->turnstile_site_key ?? '',
        ]);
    }

    /**
     * Publicly accessible download method for system assets.
     *
     * @param string $file_name The name of the file to download.
     * @return ResponseInterface|null The download response or null if not found.
     */
    public function download(string $file_name): ?ResponseInterface
    {
        $safe_name = basename($file_name);
        $path = FCPATH . 'uploads/';
        $file_path = $path . $safe_name;

        if (!is_file($file_path)) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $this->response->download($file_path, null)->setFileName($safe_name);
    }

    /**
     * Unified dashboard redirector.
     * Redirects to administrator or student dashboard based on a session role.
     *
     * @return ResponseInterface
     */
    public function dashboard(): ResponseInterface
    {
        if (session()->get('is_admin') || session()->get('is_operator')) {
            return redirect()->to('administrator/dashboard');
        }

        if (session()->get('is_student')) {
            return redirect()->to('students/dashboard');
        }

        return redirect()->to('/');
    }
}
