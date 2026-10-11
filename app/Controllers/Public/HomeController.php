<?php

declare(strict_types=1);

namespace App\Controllers\Public;

use App\Libraries\Auth;
use App\Models\AdmissionPeriodModel;
use App\Models\ApplicationSettingModel;
use App\Models\BranchModel;
use App\Controllers\BaseController;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;
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
     * A visitor who is already logged in (or has a valid "remember me" cookie)
     * is sent to the dashboard instead.
     *
     * @return string|RedirectResponse Rendered HTML view of the home page, or redirect to the dashboard.
     */
    public function index(): string|RedirectResponse
    {
        $auth = new Auth();
        if ($auth->isLoggedIn() && ($auth->isAdmin() || $auth->isOperator() || $auth->isStudent())) {
            return redirect()->to('dashboard')->withCookies();
        }

        $db = db_connect();
        $setting = $db->table('application_settings')->where('id', 1)->get()->getRow();

        $activeAdmissionYear = new AdmissionPeriodModel()->getActiveAdmissionYear();

        $branchModel = new BranchModel();
        $branches = $branchModel->where('is_deleted', false)->orderBy('id')->findAll();

        return view('public/home', [
            'institution_name' => $setting->institution_name ?? 'PSB Online',
            'tagline' => $setting->tagline ?? 'Penerimaan Santri Baru (PSB) Online',
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
     * Only the files currently configured in the application settings (logo, favicon,
     * payment method and registration terms) are served; anything else is a 404.
     *
     * @param string $file_name The name of the file to download.
     * @return ResponseInterface The download response.
     * @throws PageNotFoundException When the file is not a configured public asset or does not exist.
     */
    public function download(string $file_name = ''): ResponseInterface
    {
        $safe_name = basename($file_name);
        $setting = new ApplicationSettingModel()->getSetting();

        $allowed = [];
        foreach (['logo', 'favicon', 'payment_method', 'registration_terms'] as $field) {
            $value = basename((string) ($setting->$field ?? ''));
            if ($value !== '' && $value !== '.' && $value !== '..') {
                $allowed[$value] = $field;
            }
        }

        $file_path = public_upload_path() . $safe_name;
        if (!isset($allowed[$safe_name]) || !is_file($file_path)) {
            throw PageNotFoundException::forPageNotFound();
        }

        $response = $this->response->download($file_path, null, true)->setFileName($safe_name);

        // Images are shown in the page (logo, favicon); documents are downloaded.
        return in_array($allowed[$safe_name], ['logo', 'favicon'], true) ? $response->inline() : $response;
    }

    /**
     * Unified dashboard redirector.
     * Redirects to administrator or student dashboard based on a session role.
     *
     * @return ResponseInterface
     */
    public function dashboard(): ResponseInterface
    {
        // withCookies() sends the rotated remember-me cookie when this request logged the user in from it.
        if (session()->get('is_admin') || session()->get('is_operator')) {
            return redirect()->to('administrator/dashboard')->withCookies();
        }

        if (session()->get('is_student')) {
            return redirect()->to('students/dashboard')->withCookies();
        }

        return redirect()->to('/')->withCookies();
    }
}
