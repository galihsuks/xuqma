<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Firebase\JWT\JWT;

class AuthController extends BaseController
{ 
    public function welcome()
    {
        return $this->success('API ' . getenv('app.name', 'Base App'));
    }

    public function login()
    {
        $this->UUID = strtoupper(uniqid());
        $this->UserID = '';
        $this->RouterCode = 'AUTH-LOGIN';
        $body = $this->getBody();
        
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', ['path' => $this->request->getPath(), 'request' => $body]);
        
        // Validate input
        $rules = [
            'username'  => 'required|min_length[3]|max_length[100]',
            'password'  => 'required|min_length[6]',
        ];
        if (!$this->validateData($body, $rules)) {
            return $this->validationError($this->validator->getErrors());
        }

        try {
            $user = $this->authModel->getPasswordByUsernameOrEmail($body['username']);
            if (!$user) {
                return $this->badRequest('Username not found!');
            }
            if (!password_verify($body['password'], $user['password'])) {
                return $this->badRequest('Wrong password!');
            }

            $defaultRole = $this->authModel->getRolesByUserId($user['id']);
            $defaultRoleCode = $defaultRole['code'] ?? null;

            // generate JWT
            $key = (string) $this->getCustomEnv('JWT_SECRET', '');
            $exp = strtotime('tomorrow');
            $payload = [
                'sub' => $user['id'],
                'email' => $user['email'],
                'role' => $defaultRoleCode,
                'iat' => time(),
                'exp' => $exp,
            ];
            $token = JWT::encode($payload, $key, 'HS256');

            // simpan ke table token
            $this->tokenModel->insertToken([
                'user_id'      => $user['id'],
                'token'        => $token,
                'expired_time' => date('Y-m-d H:i:s', $exp),
                'ip'           => $this->request->getIPAddress(),
                'device'       => $this->request->getUserAgent()->getBrowser() . ' ' . $this->request->getUserAgent()->getVersion(),
                'platform'     => $this->request->getUserAgent()->getPlatform(),
            ]);

            return $this->success('Login successful!', [
                'token' => $token,
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'full_name' => $user['full_name'],
                    'email' => $user['email'],
                    'role' => $defaultRole,
                ],
            ]);
        } catch (\Throwable $th) {
            $dataCatchError = [
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
            ];
            return $this->serverError("There's a problem with the server, Contact us!", $dataCatchError);
        }
    }

    public function logout()
    {       
        $this->UUID = strtoupper(uniqid());
        $this->UserID = '';
        $this->RouterCode = 'AUTH-LOGOUT';
        
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', ['path' => $this->request->getPath()]);

        try {
            $authHeader = (string) $this->request->getHeaderLine('Authorization');
            $token = '';
            if (preg_match('/^Bearer\s+(.+)$/i', $authHeader, $matches)) {
                $token = trim($matches[1]);
            }

            if ($token !== '') {
                $this->tokenModel->destroyTokenByToken($token);
            }

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
}
