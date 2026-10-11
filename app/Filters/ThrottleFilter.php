<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Throttle\Throttler;
use Config\Services;

/**
 * Class ThrottleFilter
 *
 * Rate limits a route with a token bucket ("throttle:capacity,seconds").
 *
 * Two buckets are checked per route:
 * - the client IP address as resolved by IncomingRequest::getIPAddress()
 *   (forwarded headers are only trusted for proxies listed in Config\App::$proxyIPs);
 * - the submitted "email" field, when present, so one account cannot be targeted
 *   from many IP addresses.
 */
class ThrottleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null): ResponseInterface|RequestInterface|null
    {
        $arguments ??= [];
        $capacity = (int) ($arguments[0] ?? 60);
        $seconds  = (int) ($arguments[1] ?? 60);

        /** @var Throttler $throttler */
        $throttler = Services::throttler();

        $path = strtolower(trim($request->getUri()->getPath(), '/'));
        $keys = ['ip_' . md5($request->getIPAddress() . ':' . $path)];

        if ($request instanceof IncomingRequest) {
            $email = $request->getPost('email');
            if (is_string($email) && trim($email) !== '') {
                $keys[] = 'email_' . md5(mb_strtolower(trim($email)) . ':' . $path);
            }
        }

        foreach ($keys as $key) {
            if (!$throttler->check($key, $capacity, $seconds)) {
                $response = Services::response();
                $response->setStatusCode(429);
                $response->setJSON([
                    'success' => false,
                    'message' => 'Terlalu banyak permintaan. Silakan coba lagi dalam ' . max(1, $throttler->getTokenTime()) . ' detik.',
                ]);
                return $response;
            }
        }

        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null): void
    {
    }
}
