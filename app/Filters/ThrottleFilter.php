<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Throttle\Throttler;
use Config\Services;

class ThrottleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null): ResponseInterface|RequestInterface|null
    {
        $arguments ??= [];
        $capacity = (int) ($arguments[0] ?? 60);
        $seconds  = (int) ($arguments[1] ?? 60);

        $throttler = Services::throttler();

        $key = md5($request->getIPAddress() . ':' . $request->getUri()->getPath());

        if (!$throttler->check($key, $capacity, $seconds)) {
            $response = Services::response();
            $response->setStatusCode(429);
            $response->setJSON([
                'success' => false,
                'message' => 'Terlalu banyak permintaan. Silakan coba lagi dalam ' . $throttler->getTokenTime() . ' detik.',
            ]);
            return $response;
        }

        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null): void
    {
    }
}
