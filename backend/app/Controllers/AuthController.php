<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Firebase\JWT\JWT;

class AuthController extends BaseController
{
    private const AUTH_COOKIE_NAME = 'auth_token';
    private const LOGIN_SESSION_KEYS = [
        'is_authenticated',
        'auth_user_id',
        'auth_username',
        'auth_full_name',
        'auth_email',
        'auth_role_id',
        'auth_role_code',
        'auth_role_name',
        'auth_login_at',
    ];

    public function welcome()
    {
        return $this->success('API ' . getenv('app.name', 'Base App'));
    }

    public function loginPage()
    {
        $redirectTo = $this->resolveRedirectPath($this->request->getGet('redirect'));

        if ($this->isSessionAuthenticated()) {
            return redirect()->to($redirectTo ?: $this->getRoleHomeRoute((string) session('auth_role_code')));
        }

        return view('web/auth/login', [
            'redirectTo' => $redirectTo,
        ]);
    }

    public function loginSubmit()
    {
        $credentials = [
            'username' => trim((string) $this->request->getPost('username')),
            'password' => (string) $this->request->getPost('password'),
        ];
        $redirectTo = $this->resolveRedirectPath($this->request->getPost('redirect'));
        $errors = $this->validateLoginCredentials($credentials);

        if ($errors !== []) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        try {
            $authResult = $this->attemptAuthentication($credentials);
            if ($authResult['error'] !== null) {
                return redirect()->back()->withInput()->with('error', $authResult['error']);
            }

            $redirectResponse = redirect()
                ->to($redirectTo ?: $this->getRoleHomeRoute($authResult['user']['role']['code'] ?? null))
                ->with('success', 'Login successful!');

            $this->appendAuthCookie($redirectResponse, (string) $authResult['token'], (int) $authResult['expires_at']);

            return $redirectResponse;
        } catch (\Throwable $th) {
            log_message('error', $th->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', "There's a problem with the server, Contact us!");
        }
    }

    public function me()
    {
        $this->UUID = strtoupper(uniqid());
        $this->UserID = (string) ($this->getLoginUser('id') ?? '');
        $this->RouterCode = 'AUTH-ME';

        $userId = (string) ($this->getLoginUser('id') ?? '');
        if ($userId === '') {
            return $this->unauthorized('Unauthorized.');
        }

        $user = $this->buildAuthenticatedUser($userId);
        if ($user === null) {
            return $this->unauthorized('User session is no longer valid.');
        }

        return $this->success('Authenticated user loaded.', $user);
    }

    public function logout()
    {
        $this->UUID = strtoupper(uniqid());
        $this->UserID = (string) ($this->getLoginUser('id') ?? session('auth_user_id') ?? '');
        $this->RouterCode = 'AUTH-LOGOUT';

        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', ['path' => $this->request->getPath()]);

        try {
            $token = $this->getIncomingToken();
            $this->clearAuthState($token);

            return $this->success('Logout successfully');
        } catch (\Throwable $th) {
            $dataCatchError = [
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
            ];

            return $this->serverError("There's a problem with the server, Contact us!", $dataCatchError);
        }
    }

    private function validateLoginCredentials(array $credentials): array
    {
        $rules = [
            'username' => 'required|min_length[3]|max_length[100]',
            'password' => 'required|min_length[6]',
        ];

        if (!$this->validateData($credentials, $rules)) {
            return $this->validator->getErrors();
        }

        return [];
    }

    private function attemptAuthentication(array $credentials): array
    {
        $user = $this->authModel->getPasswordByUsernameOrEmail((string) ($credentials['username'] ?? ''));
        if (!$user) {
            return [
                'error' => 'Username not found!',
                'token' => null,
                'user' => null,
            ];
        }

        if (!password_verify((string) ($credentials['password'] ?? ''), (string) $user['password'])) {
            return [
                'error' => 'Wrong password!',
                'token' => null,
                'user' => null,
            ];
        }

        $defaultRole = $this->authModel->getRolesByUserId($user['id']);
        $defaultRoleCode = $defaultRole['code'] ?? null;
        $tokenData = $this->createJwtToken($user, $defaultRoleCode);
        $authUser = $this->formatAuthenticatedUser($user, $defaultRole);

        $this->persistToken($user['id'], $tokenData['token'], $tokenData['exp']);
        $this->persistSession($authUser);
        $this->persistCookie($tokenData['token'], $tokenData['exp']);

        return [
            'error' => null,
            'token' => $tokenData['token'],
            'expires_at' => $tokenData['exp'],
            'user' => $authUser,
        ];
    }

    private function createJwtToken(array $user, ?string $roleCode): array
    {
        $key = (string) $this->getCustomEnv('JWT_SECRET', '');
        $exp = strtotime('+1 day');
        $payload = [
            'sub' => $user['id'],
            'email' => $user['email'],
            'role' => $roleCode,
            'iat' => time(),
            'exp' => $exp,
        ];

        return [
            'token' => JWT::encode($payload, $key, 'HS256'),
            'exp' => $exp,
        ];
    }

    private function persistToken(string $userId, string $token, int $expiredAt): void
    {
        $this->tokenModel->insertToken([
            'user_id' => $userId,
            'token' => $token,
            'expired_time' => date('Y-m-d H:i:s', $expiredAt),
            'ip' => $this->request->getIPAddress(),
            'device' => $this->request->getUserAgent()->getBrowser() . ' ' . $this->request->getUserAgent()->getVersion(),
            'platform' => $this->request->getUserAgent()->getPlatform(),
        ]);
    }

    private function persistSession(array $authUser): void
    {
        $session = session();
        $session->regenerate(true);
        $session->set([
            'is_authenticated' => true,
            'auth_user_id' => $authUser['id'],
            'auth_username' => $authUser['username'],
            'auth_full_name' => $authUser['full_name'],
            'auth_email' => $authUser['email'],
            'auth_role_id' => $authUser['role']['id'] ?? null,
            'auth_role_code' => $authUser['role']['code'] ?? null,
            'auth_role_name' => $authUser['role']['name'] ?? null,
            'auth_login_at' => date('c'),
        ]);
    }

    private function persistCookie(string $token, int $expiredAt): void
    {
        $this->appendAuthCookie($this->response, $token, $expiredAt);
    }

    private function appendAuthCookie($response, string $token, int $expiredAt): void
    {
        $response->setCookie([
            'name' => self::AUTH_COOKIE_NAME,
            'value' => $token,
            'expire' => max(0, $expiredAt - time()),
            'path' => '/',
            'secure' => $this->request->isSecure(),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }

    private function clearAuthState(string $token = ''): void
    {
        if ($token !== '') {
            $this->tokenModel->destroyTokenByToken($token);
        }

        session()->remove(self::LOGIN_SESSION_KEYS);
        session()->regenerate(true);
        $this->response->deleteCookie(self::AUTH_COOKIE_NAME);
    }

    private function getIncomingToken(): string
    {
        $authHeader = (string) $this->request->getHeaderLine('Authorization');
        if (preg_match('/^Bearer\s+(.+)$/i', $authHeader, $matches)) {
            return trim($matches[1]);
        }

        return trim((string) $this->request->getCookie(self::AUTH_COOKIE_NAME));
    }

    private function buildAuthenticatedUser(string $userId): ?array
    {
        $user = $this->authModel->getUserById($userId);
        if (!$user) {
            return null;
        }

        $role = $this->authModel->getRolesByUserId($userId);

        return $this->formatAuthenticatedUser($user, $role);
    }

    private function formatAuthenticatedUser(array $user, ?array $role): array
    {
        return [
            'id' => $user['id'],
            'username' => $user['username'],
            'full_name' => $user['full_name'],
            'email' => $user['email'],
            'role' => $role ?: null,
        ];
    }

    private function resolveRedirectPath(?string $redirect): string
    {
        $redirect = trim((string) $redirect);
        if ($redirect === '') {
            return '';
        }

        if (!str_starts_with($redirect, '/') || str_starts_with($redirect, '//')) {
            return '';
        }

        return $redirect;
    }

    private function getRoleHomeRoute(?string $roleCode): string
    {
        if ($roleCode === 'C') {
            return '/shop';
        }

        return '/app/admin/dashboard';
    }

    private function isSessionAuthenticated(): bool
    {
        return (bool) session('is_authenticated');
    }
}
