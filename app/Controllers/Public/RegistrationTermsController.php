<?php

declare(strict_types=1);

namespace App\Controllers\Public;

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
     * @param string $filename The name of the file to download.
     * @return DownloadResponse|null The download response or null if not found.
     */
    public function file(string $filename): ?DownloadResponse
    {
        $safe_name = basename($filename);
        $path = FCPATH . 'uploads/' . $safe_name;

        if (is_file($path)) {
            return $this->response->download($path, null);
        } else {
            throw PageNotFoundException::forPageNotFound('File tidak ditemukan');
        }
    }
}
