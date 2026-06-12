<?php

namespace App\Filters;

use CodeIgniter\Config\Services;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class Cors implements FilterInterface
{
    private function getAllowedOrigins(): array
    {
        $allowedEnv = env('ALLOWED_ORIGINS');
        if (!$allowedEnv) {
            return [];
        }

        return array_values(array_filter(array_map(
            static fn (string $origin): string => rtrim(trim($origin), '/'),
            explode(',', $allowedEnv),
        )));
    }

    private function applyCorsHeaders(ResponseInterface $response, string $origin): void
    {
        $response->setHeader('Access-Control-Allow-Origin', $origin);
        $response->setHeader('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With, X-Request-Id, X-Signature, X-Timestamp, Authorization');
        $response->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, DELETE');
        $response->setHeader('Access-Control-Allow-Credentials', 'true');
        $response->setHeader('Vary', 'Origin');
    }

    public function before(RequestInterface $request, $arguments = null)
    {
        $origin = rtrim(trim($request->getHeaderLine('Origin')), '/');
        $allowedOrigins = $this->getAllowedOrigins();
        $response = Services::response();

        if ($origin !== '' && in_array($origin, $allowedOrigins, true)) {
            $this->applyCorsHeaders($response, $origin);
        }

        if (strtoupper($request->getMethod()) === 'OPTIONS') {
            return $response->setStatusCode(204);
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        $origin = rtrim(trim($request->getHeaderLine('Origin')), '/');
        $allowedOrigins = $this->getAllowedOrigins();

        if ($origin !== '' && in_array($origin, $allowedOrigins, true)) {
            $this->applyCorsHeaders($response, $origin);
        }

        return $response;
    }
}
