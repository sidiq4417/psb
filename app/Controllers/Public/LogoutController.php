<?php

declare(strict_types=1);

namespace App\Controllers\Public;

use App\Libraries\Auth;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Class LogoutController
 *
 * Handles user logout functionality and session termination.
 *
 * @package App\Controllers\Public
 */
class LogoutController extends BaseController
{
    /**
     * Authentication library instance.
     *
     * Used to manage login state and perform logout operations.
     *
     * @var Auth
     */
    protected Auth $auth;

    /**
     * LogoutController Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->auth = new Auth();
    }

    /**
     * Handle user logout.
     *
     * @return RedirectResponse Redirects to the login page.
     */
    public function index(): RedirectResponse
    {
        if (! $this->auth->isLoggedIn()) {
            return redirect()->to('/');
        }
        $this->auth->logout();
        return redirect()->to('/')->withCookies();
    }
}
