<?php

namespace App\Controllers;

class DropdownController extends BaseController
{
    public function role()
    {
        $this->UUID = strtoupper(uniqid());
        $this->UserID = $this->request->user['id'] ?? '';
        $this->RouterCode = 'DROPDOWN-ROLE';
        $keywords = trim((string) ($this->request->getGet('keywords') ?? ''));
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', [
            'path' => $this->request->getPath(),
            'query' => $this->request->getGet(),
        ]);

        $data = $this->roleModel->getDropdownOptions($keywords);
        return $this->success('Role dropdown list', $data);
    }

    public function user()
    {
        $this->UUID = strtoupper(uniqid());
        $this->UserID = $this->request->user['id'] ?? '';
        $this->RouterCode = 'DROPDOWN-USER';
        $keywords = trim((string) ($this->request->getGet('keywords') ?? ''));
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', [
            'path' => $this->request->getPath(),
            'query' => $this->request->getGet(),
        ]);

        $data = $this->userModel->getDropdownOptions($keywords);
        return $this->success('User dropdown list', $data);
    }
}
