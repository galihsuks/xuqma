<?php

namespace App\Controllers;

class RoleController extends BaseController
{
    public function index()
    {
        $this->UUID = strtoupper(uniqid());
        $this->UserID = $this->request->user['id'] ?? '';
        $this->RouterCode = 'ROLE-INDEX';
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $defaultPageSize = (int) $this->roleModel->LIMIT_DATA;
        $pageSize = max(1, min(100, (int) ($this->request->getGet('page_size') ?? $defaultPageSize)));
        $keywords = trim((string) ($this->request->getGet('keywords') ?? ''));
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', ['path' => $this->request->getPath()]);

        $result = $this->roleModel->getRoleListPaginated($page, $pageSize, $keywords);
        $items = $result['items'];
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

        return $this->success('List role', $items, $pagination);
    }

    public function detail($id)
    {
        $this->UUID = strtoupper(uniqid());
        $this->UserID = $this->request->user['id'] ?? '';
        $this->RouterCode = 'ROLE-DETAIL';
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', ['path' => $this->request->getPath()]);

        $data = $this->roleModel->getRoleById($id);
        if (!$data) {
            return $this->notFound('Role not found!');
        }
        return $this->success('Detail role', $data);
    }

    public function create()
    {
        $this->UUID = strtoupper(uniqid());
        $this->UserID = $this->request->user['id'] ?? '';
        $this->RouterCode = 'ROLE-CREATE';
        $body = $this->getBody();
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', ['path' => $this->request->getPath(), 'request' => $body]);

        $rules = [
            'code' => 'required|min_length[2]|max_length[50]|alpha_numeric_punct',
            'name' => 'required|min_length[2]|max_length[100]',
            'description' => 'permit_empty|max_length[255]',
        ];
        if (!$this->validateData($body, $rules)) {
            return $this->validationError($this->validator->getErrors());
        }

        if ($this->roleModel->getRoleByCode($body['code'])) {
            return $this->badRequest('Role code already used!');
        }

        try {
            $created = $this->roleModel->insertRole([
                'code' => $body['code'],
                'name' => $body['name'],
                'description' => $body['description'] ?? null,
            ]);
            return $this->created('Role created successfully', $created);
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
        $this->RouterCode = 'ROLE-EDIT';
        $body = $this->getBody();
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', ['path' => $this->request->getPath(), 'request' => $body]);

        $current = $this->roleModel->getRoleById($id);
        if (!$current) {
            return $this->notFound('Role not found!');
        }

        $rules = [
            'code' => 'required|min_length[2]|max_length[50]|alpha_numeric_punct',
            'name' => 'required|min_length[2]|max_length[100]',
            'description' => 'permit_empty|max_length[255]',
        ];
        if (!$this->validateData($body, $rules)) {
            return $this->validationError($this->validator->getErrors());
        }

        $duplicate = $this->roleModel->getRoleByCode($body['code']);
        if ($duplicate && $duplicate['id'] !== $id) {
            return $this->badRequest('Role code already used!');
        }

        try {
            $updated = $this->roleModel->updateRoleById($id, [
                'code' => $body['code'],
                'name' => $body['name'],
                'description' => $body['description'] ?? null,
            ]);
            return $this->success('Role updated successfully', $updated);
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
        $this->RouterCode = 'ROLE-DESTROY';
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', ['path' => $this->request->getPath()]);

        try {
            $deleted = $this->roleModel->destroyRoleById($id);
            if (!$deleted) {
                return $this->notFound('Role not found!');
            }
            return $this->success('Role deleted successfully', $deleted);
        } catch (\Throwable $th) {
            return $this->serverError("There's a problem with the server, Contact us!", [
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
            ]);
        }
    }
}
