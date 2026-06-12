<?php

namespace App\Controllers;

class MenuController extends BaseController
{
    public function index()
    {
        $this->UUID = strtoupper(uniqid());
        $this->UserID = $this->request->user['id'] ?? '';
        $this->RouterCode = 'MENU-INDEX';
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', ['path' => $this->request->getPath()]);

        $menus = $this->menuModel
            ->select('id, parent_menu_id, name, description, url, `group`, icon, display, sort')
            ->findAll();

        $menuByParent = [];
        foreach ($menus as $menu) {
            $parentId = $menu['parent_menu_id'] ?? null;
            if ($parentId === '') {
                $parentId = null;
            }
            if (!array_key_exists($parentId, $menuByParent)) {
                $menuByParent[$parentId] = [];
            }
            $menuByParent[$parentId][] = $menu;
        }

        $groups = ['main', 'system'];
        $data = [];
        foreach ($groups as $groupName) {
            $rootMenus = array_values(array_filter(
                $menuByParent[null] ?? [],
                static fn ($menu) => ($menu['group'] ?? null) === $groupName,
            ));

            usort($rootMenus, [$this, 'sortMenuItems']);
            $groupChildren = [];
            foreach ($rootMenus as $rootMenu) {
                $groupChildren[] = $this->buildMenuNode($rootMenu, $menuByParent);
            }

            $data[] = [
                'group' => $groupName,
                'group_children' => $groupChildren,
            ];
        }

        return $this->success('List menu', $data);
    }

    public function detail($id)
    {
        $this->UUID = strtoupper(uniqid());
        $this->UserID = $this->request->user['id'] ?? '';
        $this->RouterCode = 'MENU-DETAIL';
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', ['path' => $this->request->getPath()]);

        $data = $this->menuModel->getMenuById($id);
        if (!$data) {
            return $this->notFound('Menu not found!');
        }
        return $this->success('Detail menu', $data);
    }

    public function create()
    {
        $this->UUID = strtoupper(uniqid());
        $this->UserID = $this->request->user['id'] ?? '';
        $this->RouterCode = 'MENU-CREATE';
        $body = $this->getBody();
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', ['path' => $this->request->getPath(), 'request' => $body]);

        $rules = [
            'parent_menu_id' => 'permit_empty|is_not_unique[app_menus.id]',
            'name' => 'required|min_length[2]|max_length[100]',
            'description' => 'permit_empty|max_length[255]',
            'url' => 'permit_empty|max_length[255]',
            'group' => 'required|in_list[main,system]',
            'icon' => 'permit_empty|max_length[100]',
            'display' => 'required|in_list[0,1]',
            'sort' => 'required|integer',
        ];
        if (!$this->validateData($body, $rules)) {
            return $this->validationError($this->validator->getErrors());
        }

        try {
            $created = $this->menuModel->insertMenu([
                'parent_menu_id' => $body['parent_menu_id'] ?: null,
                'name' => $body['name'],
                'description' => $body['description'] ?? null,
                'url' => $body['url'] ?? null,
                'group' => $body['group'],
                'icon' => $body['icon'] ?? null,
                'display' => filter_var($body['display'], FILTER_VALIDATE_BOOLEAN),
                'sort' => (int) $body['sort'],
            ]);
            return $this->created('Menu created successfully', $created);
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
        $this->RouterCode = 'MENU-EDIT';
        $body = $this->getBody();
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', ['path' => $this->request->getPath(), 'request' => $body]);

        $current = $this->menuModel->getMenuById($id);
        if (!$current) {
            return $this->notFound('Menu not found!');
        }

        $rules = [
            'parent_menu_id' => 'permit_empty|is_not_unique[app_menus.id]',
            'name' => 'required|min_length[2]|max_length[100]',
            'description' => 'permit_empty|max_length[255]',
            'url' => 'permit_empty|max_length[255]',
            'group' => 'required|in_list[main,system]',
            'icon' => 'permit_empty|max_length[100]',
            'display' => 'required|in_list[0,1]',
            'sort' => 'required|integer',
        ];
        if (!$this->validateData($body, $rules)) {
            return $this->validationError($this->validator->getErrors());
        }

        if (($body['parent_menu_id'] ?? null) === $id) {
            return $this->badRequest('Parent menu cannot be itself!');
        }

        try {
            $updated = $this->menuModel->updateMenuById($id, [
                'parent_menu_id' => ($body['parent_menu_id'] ?? '') !== '' ? $body['parent_menu_id'] : null,
                'name' => $body['name'],
                'description' => $body['description'] ?? null,
                'url' => $body['url'] ?? null,
                'group' => $body['group'],
                'icon' => $body['icon'] ?? null,
                'display' => filter_var($body['display'], FILTER_VALIDATE_BOOLEAN),
                'sort' => (int) $body['sort'],
            ]);
            return $this->success('Menu updated successfully', $updated);
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
        $this->RouterCode = 'MENU-DESTROY';
        $forceDelete = (string) ($this->request->getGet('force_delete') ?? '') === '1';
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', [
            'path' => $this->request->getPath(),
            'query' => $this->request->getGet(),
        ]);

        try {
            $menu = $this->menuModel->getMenuById($id);
            if (!$menu) {
                return $this->notFound('Menu not found!');
            }

            $menuIds = $this->collectMenuTreeIds((string) $id);
            $hasChildren = count($menuIds) > 1;
            $roleUsageCount = $this->roleMenuControlModel
                ->whereIn('menu_id', $menuIds)
                ->countAllResults();

            if (!$forceDelete && ($hasChildren || $roleUsageCount > 0)) {
            return $this->badRequest('Menu is still referenced by other menus or roles!');
            }

            if ($forceDelete) {
                $this->deleteMenuTree($menuIds);
                return $this->success('Menu deleted successfully', [
                    'deleted_menu_ids' => $menuIds,
                ]);
            }

            $deleted = $this->menuModel->destroyMenuById($id);
            return $this->success('Menu deleted successfully', $deleted);
        } catch (\Throwable $th) {
            return $this->serverError("There's a problem with the server, Contact us!", [
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
            ]);
        }
    }

    private function sortMenuItems(array $a, array $b): int
    {
        $sortA = (int) ($a['sort'] ?? 0);
        $sortB = (int) ($b['sort'] ?? 0);
        if ($sortA === $sortB) {
            return strcmp((string) ($a['name'] ?? ''), (string) ($b['name'] ?? ''));
        }
        return $sortA <=> $sortB;
    }

    private function buildMenuNode(array $menu, array $menuByParent): array
    {
        $children = $menuByParent[$menu['id']] ?? [];
        usort($children, [$this, 'sortMenuItems']);

        $result = [
            'id' => $menu['id'],
            'name' => $menu['name'],
            'description' => $menu['description'],
            'url' => $menu['url'],
            'group' => $menu['group'],
            'icon' => $menu['icon'],
            'display' => (string) ($menu['display'] ? '1' : '0'),
            'sort' => (string) ($menu['sort'] ?? '0'),
        ];

        if ($children !== []) {
            $result['chilren'] = array_map(
                fn ($child) => $this->buildMenuNode($child, $menuByParent),
                $children,
            );
        }

        return $result;
    }

    private function collectMenuTreeIds(string $rootMenuId): array
    {
        $allMenus = $this->menuModel->select('id, parent_menu_id')->findAll();
        $childrenMap = [];
        foreach ($allMenus as $menu) {
            $parentId = $menu['parent_menu_id'] ?? null;
            if ($parentId === null || $parentId === '') {
                continue;
            }
            if (!array_key_exists($parentId, $childrenMap)) {
                $childrenMap[$parentId] = [];
            }
            $childrenMap[$parentId][] = $menu['id'];
        }

        $result = [];
        $stack = [$rootMenuId];
        while ($stack !== []) {
            $currentId = array_pop($stack);
            if (in_array($currentId, $result, true)) {
                continue;
            }
            $result[] = $currentId;
            foreach ($childrenMap[$currentId] ?? [] as $childId) {
                $stack[] = $childId;
            }
        }

        return $result;
    }

    private function deleteMenuTree(array $menuIds): void
    {
        $menuControlRows = $this->menuControlModel
            ->select('id')
            ->whereIn('menu_id', $menuIds)
            ->findAll();
        $menuControlIds = array_values(array_map(static fn ($row) => $row['id'], $menuControlRows));

        if ($menuControlIds !== []) {
            $this->roleMenuControlModel
                ->whereIn('menu_control_id', $menuControlIds)
                ->delete();
        }

        $this->roleMenuControlModel
            ->whereIn('menu_id', $menuIds)
            ->delete();

        $this->menuControlModel
            ->whereIn('menu_id', $menuIds)
            ->delete();

        $this->menuModel
            ->whereIn('id', $menuIds)
            ->delete();
    }
}
