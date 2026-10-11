<?php

declare(strict_types=1);

namespace App\Controllers\Public;

use App\Controllers\BaseController;

/**
 * Class UnauthorizedController
 *
 * Handles the display of the unauthorized access page.
 *
 * @package App\Controllers\Public
 */
class UnauthorizedController extends BaseController
{
    /**
     * Display unauthorized view.
     *
     * @return string Rendered HTML view with error details.
     */
    public function index(): string
    {
        $vars = [
            'title' => 'Akses Ditolak',
            'error_code' => '401 Unauthorized',
            'error_message' => 'Maaf, Anda tidak memiliki hak akses untuk halaman ini.',
        ];
        return view('public/unauthorized', $vars);
    }
}
