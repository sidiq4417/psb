<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;


/**
 * Class AuthFilter
 *
 * Authentication and authorization filter for the application.
 *
 * Responsibilities:
 * - Ensures the user is logged in by checking the `actor` session key.
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

        // Check if user is logged in
        if (!($session->has('actor') && $session->get('actor') > 0)) {
            return redirect()->to('/');
        }

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
