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
            'title' => 'Unauthorized',
            'error_code' => '401 Unauthorized',
            'error_message' => 'You don’t have permission to access this page.',
        ];
        return view('public/unauthorized', $vars);
    }
}
