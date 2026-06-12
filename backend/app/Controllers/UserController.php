<?php

namespace App\Controllers;

class UserController extends BaseController
{
    public function index()
    {
        $this->UUID = strtoupper(uniqid());
        $this->UserID = $this->request->user['id'] ?? '';
        $this->RouterCode = 'USER-INDEX';
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $defaultPageSize = (int) $this->userModel->LIMIT_DATA;
        $pageSize = max(1, min(100, (int) ($this->request->getGet('page_size') ?? $defaultPageSize)));
        $keywords = trim((string) ($this->request->getGet('keywords') ?? ''));
        $roleId = trim((string) ($this->request->getGet('role_id') ?? ''));
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', [
            'path' => $this->request->getPath(),
            'query' => $this->request->getGet(),
        ]);

        $result = $this->userModel->getUserListPaginated($page, $pageSize, $keywords, $roleId);
        $users = $result['items'];
        $totalItems = (int) $result['total_items'];

        $totalPages = max(1, (int) ceil($totalItems / $pageSize));
        $pagination = [
            'page' => $page,
            'page_size' => $pageSize,
            'total_items' => $totalItems,
            'total_pages' => $totalPages,
            'has_next' => $page < $totalPages,
            'has_prev' => $page > 1,
        ];

        return $this->success('List user', $users, $pagination);
    }

    public function detail($id)
    {
        $this->UUID = strtoupper(uniqid());
        $this->UserID = $this->request->user['id'] ?? '';
        $this->RouterCode = 'USER-DETAIL';
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', ['path' => $this->request->getPath()]);

        $user = $this->userModel->getUserById($id);
        if (!$user) {
            return $this->notFound('User not found!');
        }
        unset($user['password']);
        $user['role'] = $this->authModel->getRolesByUserId($id);

        return $this->success('Detail user', $user);
    }

    public function create()
    {
        $this->UUID = strtoupper(uniqid());
        $this->UserID = $this->request->user['id'] ?? '';
        $this->RouterCode = 'USER-CREATE';
        $body = $this->getBody();
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', ['path' => $this->request->getPath(), 'request' => $body]);

        $rules = [
            'username' => 'required|min_length[3]|max_length[100]',
            'full_name' => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email|max_length[150]',
            'password' => 'required|min_length[6]|max_length[255]',
            'role_id' => 'required',
        ];
        if (!$this->validateData($body, $rules)) {
            return $this->validationError($this->validator->getErrors());
        }

        $existing = $this->userModel->getUserByEmailOrUsername($body['email'], $body['username']);
        if ($existing) {
            return $this->badRequest('Email or username already used!');
        }

        $roleId = (string) ($body['role_id'] ?? '');
        if ($roleId === '' || !$this->isValidRoleId($roleId)) {
            return $this->badRequest('role_id is invalid!');
        }

        try {
            $created = $this->userModel->insertUser([
                'username' => $body['username'],
                'full_name' => $body['full_name'],
                'email' => $body['email'],
                'password' => password_hash($body['password'], PASSWORD_BCRYPT),
            ]);

            $this->userRoleModel->replaceByUserId($created['id'], $roleId);

            unset($created['password']);
            $created['role'] = $this->authModel->getRolesByUserId($created['id']);

            return $this->created('User created successfully', $created);
        } catch (\Throwable $th) {
            return $this->serverError("There's a problem with the server, Contact us!", [
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
            ]);
        }
    }

    public function edit($id)
    {
        $this->UUID = strtoupper(uniqid());
        $this->UserID = $this->request->user['id'] ?? '';
        $this->RouterCode = 'USER-EDIT';
        $body = $this->getBody();
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', ['path' => $this->request->getPath(), 'request' => $body]);

        $current = $this->userModel->getUserById($id);
        if (!$current) {
            return $this->notFound('User not found!');
        }

        $rules = [
            'username' => 'required|min_length[3]|max_length[100]',
            'full_name' => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email|max_length[150]',
            'password' => 'permit_empty|min_length[6]|max_length[255]',
            'role_id' => 'permit_empty',
        ];
        if (!$this->validateData($body, $rules)) {
            return $this->validationError($this->validator->getErrors());
        }

        $duplicate = $this->userModel
            ->select('id')
            ->groupStart()
                ->where('email', $body['email'])
                ->orWhere('username', $body['username'])
            ->groupEnd()
            ->where('id !=', $id)
            ->first();
        if ($duplicate) {
            return $this->badRequest('Email or username already used!');
        }

        $roleId = isset($body['role_id']) ? (string) $body['role_id'] : null;
        if ($roleId !== null && $roleId !== '' && !$this->isValidRoleId($roleId)) {
            return $this->badRequest('role_id is invalid!');
        }

        $params = [
            'username' => $body['username'],
            'full_name' => $body['full_name'],
            'email' => $body['email'],
        ];
        if (!empty($body['password'])) {
            $params['password'] = password_hash($body['password'], PASSWORD_BCRYPT);
        }

        try {
            $updated = $this->userModel->updateUserById($id, $params);
            if ($roleId !== null && $roleId !== '') {
                $this->userRoleModel->replaceByUserId($id, $roleId);
            }

            unset($updated['password']);
            $updated['role'] = $this->authModel->getRolesByUserId($id);

            return $this->success('User updated successfully', $updated);
        } catch (\Throwable $th) {
            return $this->serverError("There's a problem with the server, Contact us!", [
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
            ]);
        }
    }

    public function destroy($id)
    {
        $this->UUID = strtoupper(uniqid());
        $this->UserID = $this->request->user['id'] ?? '';
        $this->RouterCode = 'USER-DESTROY';
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', ['path' => $this->request->getPath()]);

        try {
            $deleted = $this->userModel->destroyUserById($id);
            if (!$deleted) {
                return $this->notFound('User not found!');
            }
            $this->userRoleModel->where('user_id', $id)->delete();

            unset($deleted['password']);
            return $this->success('User deleted successfully', $deleted);
        } catch (\Throwable $th) {
            return $this->serverError("There's a problem with the server, Contact us!", [
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
            ]);
        }
    }

    private function isValidRoleId(string $roleId): bool
    {
        return $this->roleModel->where('id', $roleId)->countAllResults() > 0;
    }
}
