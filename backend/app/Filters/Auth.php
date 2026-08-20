<?php

namespace App\Filters;

use App\Models\LogModel;
use App\Models\TokenModel;
use CodeIgniter\Config\Services;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Auth implements FilterInterface
{
    private const AUTH_COOKIE_NAME = 'auth_token';
    protected $tokenModel;
    protected $logModel;

    public function __construct()
    {
        $this->tokenModel = new TokenModel();
        $this->logModel = new LogModel();
    }

    public function before(RequestInterface $request, $arguments = null)
    {
        $response = Services::response();

        if (strtoupper($request->getMethod()) === 'OPTIONS') {
            return $response->setStatusCode(204);
        }

        // ambil origin dari header
        $origin = $request->getHeaderLine('Origin');
        $allowedEnv = env('ALLOWED_ORIGINS');
        $allowedOrigins = $allowedEnv ? array_map('trim', explode(',', $allowedEnv)) : [];

        // kalau origin diizinkan, tambahkan header cors ke response error juga
        if (in_array($origin, $allowedOrigins)) {
            $response->setHeader('Access-Control-Allow-Origin', $origin);
            $response->setHeader('Access-Control-Allow-Credentials', 'true');
            $response->setHeader('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With, X-Request-Id, Authorization, X-Signature, X-Timestamp');
        }

        $token = $this->resolveToken($request);

        if (!$token) {
            $this->logModel->warning('[401] Token not found', [], $request->getIPAddress());
            return $response->setJSON(['message' => 'Token required'])->setStatusCode(401);
        }

        try {
            $record = $this->tokenModel->getTokenByToken($token);
            if (!$record) {
                $this->logModel->warning('[401] Token not registered / already revoked', ['token' => $token], $request->getIPAddress());
                return $response->setJSON(['message' => 'Token not registered / already revoked'])->setStatusCode(401);
            }

            if (strtotime($record['expired_time']) < time()) {
                $this->tokenModel->destroyTokenByToken($token);
                $this->logModel->warning('[401] Token expired', ['token' => $token], $request->getIPAddress());
                return $response->setJSON(['message' => 'Token expired'])->setStatusCode(401);
            }

            $decoded = JWT::decode($token, new Key(getenv('JWT_SECRET'), 'HS256'));
            $role = isset($decoded->role) && is_string($decoded->role) ? $decoded->role : null;

            $request->user = [
                'id'    => $decoded->sub,
                'email' => $decoded->email,
                'role'  => $role,
                'token' => $token,
            ];
        } catch (\Exception $e) {
            $this->tokenModel->destroyTokenByToken($token);
            $this->logModel->error('[401] ' . $e->getMessage(), ['token' => $token], $request->getIPAddress());
            return $response->setJSON(['message' => $e->getMessage()])->setStatusCode(401);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return $response;
    }

    private function resolveToken(RequestInterface $request): ?string
    {
        $header = (string) $request->getHeaderLine('Authorization');
        if (preg_match('/Bearer\s(\S+)/', $header, $matches)) {
            return $matches[1];
        }

        $cookieToken = trim((string) $request->getCookie(self::AUTH_COOKIE_NAME));

        return $cookieToken !== '' ? $cookieToken : null;
    }
}
