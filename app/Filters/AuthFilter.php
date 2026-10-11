<?php

namespace App\Filters;

use App\Libraries\Auth;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;


/**
 * Class AuthFilter
 *
 * Authentication and authorization filter for the application.
 *
 * Responsibilities:
 * - Ensures the user is logged in by checking the `actor` session key, or restores
 *   the session from a valid "remember me" cookie.
 * - Re-reads the user row on every request: a deleted account is logged out and
 *   the session role flags are refreshed from the database.
 * - Refreshes the branding cached in the session (institution name, logo, favicon)
 *   from the application settings, so a replaced logo or favicon shows for every user.
 * - Validates user roles based on the first URI segment (e.g., administrator, student).
 * - Redirects to the login page if not authenticated.
 * - Redirects to the unauthorized page if the user does not have the required role.
 */
class AuthFilter implements FilterInterface
{
    /**
     * Executed before the controller is called.
     *
     * @param RequestInterface $request   The current HTTP request instance.
     * @param array|null       $arguments Optional arguments passed from filter configuration.
     *
     * @return RequestInterface|ResponseInterface|string|null
     *         - RedirectResponse to `/` if the user is not authenticated.
     *         - RedirectResponse to `/unauthorized` if the user lacks the required role.
     *         - RequestInterface if the request passes validation and continues execution.
     */
    public function before(RequestInterface $request, $arguments = null): RequestInterface|ResponseInterface|string|null
    {
        $session = session();

        // Check if user is logged in (falls back to the "remember me" cookie)
        if (!($session->has('actor') && $session->get('actor') > 0) && !new Auth()->isLoggedIn()) {
            return redirect()->to('/');
        }

        // Re-check the account on every request so a deleted user loses access
        // and role changes take effect without logging in again.
        $user = db_connect()->table('users')
            ->select('is_deleted, is_admin, is_operator, is_student')
            ->where('id', (int)$session->get('actor'))
            ->get()
            ->getRow();
        if ($user === null || (int)$user->is_deleted === 1) {
            // Removes the remember-me token and cookie, clears the session keys and regenerates the ID.
            new Auth()->logout();
            return redirect()->to('/')->withCookies();
        }
        $session->set('is_admin', (bool)$user->is_admin);
        $session->set('is_operator', (bool)$user->is_operator);
        $session->set('is_student', (bool)$user->is_student);

        // Keep the branding stored in the session (institution name, sidebar logo, favicon) in sync with
        // the settings, so a logo or favicon replaced by the administrator (the old file is deleted)
        // does not break for users who were already logged in.
        Auth::cacheBranding(
            db_connect()->table('application_settings')
                ->select('institution_name, logo, favicon')
                ->where('id', 1)
                ->get()
                ->getRow()
        );

        // If arguments are provided, use them to check specifically
        if (!empty($arguments)) {
            $has_role = false;
            foreach ($arguments as $role) {
                if ($role === 'admin' && $session->get('is_admin')) {
                    $has_role = true;
                    break;
                }
                if ($role === 'operator' && $session->get('is_operator')) {
                    $has_role = true;
                    break;
                }
                if ($role === 'student' && $session->get('is_student')) {
                    $has_role = true;
                    break;
                }
            }

            if (!$has_role) {
                return redirect()
                    ->to('/unauthorized')
                    ->withCookies()
                    ->with('error', 'Maaf, Anda tidak memiliki hak akses untuk halaman ini.');
            }
            return $request;
        }

        // Fallback for compatibility: Map URI segments to session role flags
        $uri = service('uri');
        $segment = $uri->getSegment(1);
        $roleMap = [
            'administrator' => ['is_admin', 'is_operator'],
            'students' => 'is_student',
        ];

        // Validate role based on URI segment
        if (isset($roleMap[$segment])) {
            $requiredRoles = is_array($roleMap[$segment]) ? $roleMap[$segment] : [$roleMap[$segment]];
            $hasAccess = false;
            foreach ($requiredRoles as $sessionKey) {
                if ($session->get($sessionKey)) {
                    $hasAccess = true;
                    break;
                }
            }

            if (!$hasAccess) {
                return redirect()
                    ->to('/unauthorized')
                    ->withCookies()
                    ->with('error', 'Maaf, Anda tidak memiliki hak akses untuk halaman ini.');
            }
        }

        return $request;
    }

    /**
     * Executed after the controller has finished processing.
     *
     * @param RequestInterface  $request   The current HTTP request instance.
     * @param ResponseInterface $response  The HTTP response that will be sent to the client.
     * @param array|null        $arguments Optional arguments passed from filter configuration.
     *
     * @return void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Optional: Add post-processing logic such as logging or response modification
    }
}
