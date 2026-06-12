<?php

namespace App\Controllers;

class MenuControlController extends BaseController
{
    public function index($menuId)
    {
        $this->UUID = strtoupper(uniqid());
        $this->UserID = $this->request->user['id'] ?? '';
        $this->RouterCode = 'MENU-CONTROL-INDEX';
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', [
            'path' => $this->request->getPath(),
            'menu_id' => $menuId,
        ]);

        $menuId = (string) $menuId;
        if ($menuId === '') {
            return $this->badRequest('menu_id is required');
        }

        if (!$this->menuModel->getMenuById($menuId)) {
            return $this->notFound('Menu not found!');
        }

        $data = $this->menuControlModel
            ->select('id, menu_id, code, name, created_at, updated_at')
            ->where('menu_id', $menuId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
        return $this->success('List menu control', $data);
    }

    public function create()
    {
        $this->UUID = strtoupper(uniqid());
        $this->UserID = $this->request->user['id'] ?? '';
        $this->RouterCode = 'MENU-CONTROL-CREATE';
        $body = $this->getBody();
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', ['path' => $this->request->getPath(), 'request' => $body]);

        $rules = [
            'menu_id' => 'required|is_not_unique[app_menus.id]',
            'code' => 'required|min_length[1]|max_length[100]|alpha_numeric_punct',
            'name' => 'required|min_length[2]|max_length[100]',
        ];
        if (!$this->validateData($body, $rules)) {
            return $this->validationError($this->validator->getErrors());
        }

        if ($this->menuControlModel->getByMenuAndCode($body['menu_id'], $body['code'])) {
            return $this->badRequest('Menu control code already used in this menu!');
        }

        try {
            $created = $this->menuControlModel->insertMenuControl([
                'menu_id' => $body['menu_id'],
                'code' => $body['code'],
                'name' => $body['name'],
            ]);
            return $this->created('Menu control created successfully', $created);
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
        $this->RouterCode = 'MENU-CONTROL-EDIT';
        $body = $this->getBody();
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', ['path' => $this->request->getPath(), 'request' => $body]);

        $current = $this->menuControlModel->getMenuControlById($id);
        if (!$current) {
            return $this->notFound('Menu control not found!');
        }

        $rules = [
            'menu_id' => 'required|is_not_unique[app_menus.id]',
            'code' => 'required|min_length[1]|max_length[100]|alpha_numeric_punct',
            'name' => 'required|min_length[2]|max_length[100]',
        ];
        if (!$this->validateData($body, $rules)) {
            return $this->validationError($this->validator->getErrors());
        }

        $duplicate = $this->menuControlModel->getByMenuAndCode($body['menu_id'], $body['code']);
        if ($duplicate && $duplicate['id'] !== $id) {
            return $this->badRequest('Menu control code already used in this menu!');
        }

        try {
            $updated = $this->menuControlModel->updateMenuControlById($id, [
                'menu_id' => $body['menu_id'],
                'code' => $body['code'],
                'name' => $body['name'],
            ]);
            return $this->success('Menu control updated successfully', $updated);
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
        $this->RouterCode = 'MENU-CONTROL-DESTROY';
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', ['path' => $this->request->getPath()]);

        try {
            $this->roleMenuControlModel
                ->where('menu_control_id', $id)
                ->delete();

            $deleted = $this->menuControlModel->destroyMenuControlById($id);
            if (!$deleted) {
                return $this->notFound('Menu control not found!');
            }
            return $this->success('Menu control deleted successfully', $deleted);
        } catch (\Throwable $th) {
            return $this->serverError("There's a problem with the server, Contact us!", [
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
            ]);
        }
    }
}
