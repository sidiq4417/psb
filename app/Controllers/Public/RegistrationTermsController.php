<?php

declare(strict_types=1);

namespace App\Controllers\Public;

use App\Models\ApplicationSettingModel;
use CodeIgniter\Controller;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\DownloadResponse;

/**
 * Class RegistrationTermsController
 *
 * Handles the retrieval and download of registration terms and conditions documents.
 *
 * @package App\Controllers\Public
 */
class RegistrationTermsController extends Controller
{
    /**
     * Download the registration terms file.
     *
     * Only the file currently configured as "registration_terms" in the application
     * settings is served; any other name is a 404.
     *
     * @param string $filename The name of the file to download.
     * @return DownloadResponse The download response.
     * @throws PageNotFoundException When the file is not the configured registration terms or does not exist.
     */
    public function file(string $filename = ''): DownloadResponse
    {
        $safe_name = basename($filename);
        $setting = new ApplicationSettingModel()->getSetting();
        $registration_terms = basename((string) ($setting->registration_terms ?? ''));
        $path = public_upload_path() . $safe_name;

        if ($registration_terms === '' || $safe_name !== $registration_terms || !is_file($path)) {
            throw PageNotFoundException::forPageNotFound('File tidak ditemukan');
        }

        return $this->response->download($path, null, true)->setFileName($safe_name);
    }
}
