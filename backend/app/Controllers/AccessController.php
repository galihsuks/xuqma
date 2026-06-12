<?php

namespace App\Controllers;

class AccessController extends BaseController
{
    public function menu()
    {
        $this->UUID = strtoupper(uniqid());
        $this->UserID = $this->getLoginUser('id') ?? '';
        $this->RouterCode = 'ACCESS-MENU';
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', [
            'path' => $this->request->getPath(),
        ]);

        $roleCode = (string) ($this->getLoginUser('role') ?? '');
        if ($roleCode === '') {
            return $this->forbidden('User role not found!');
        }

        $role = $this->roleModel->getRoleByCode($roleCode);
        if (!$role) {
            return $this->forbidden('Role not found!');
        }

        $allMenus = $this->menuModel
            ->select('id, parent_menu_id, name, description, url, `group`, icon, display, sort')
            ->findAll();
        $rows = $this->roleMenuControlModel
            ->select('app_role_menu_controls.menu_id')
            ->join('app_menu_controls', 'app_menu_controls.id = app_role_menu_controls.menu_control_id', 'inner')
            ->where('app_role_menu_controls.role_id', $role['id'])
            ->where('app_menu_controls.code', 'R')
            ->findAll();
        $allowedMenuIds = array_values(array_unique(array_map(static fn ($row) => $row['menu_id'], $rows)));

        $menuByParent = [];
        foreach ($allMenus as $menu) {
            if (!in_array($menu['id'], $allowedMenuIds, true)) {
                continue;
            }

            $parentId = $menu['parent_menu_id'] ?? null;
            if ($parentId === '' || !in_array($parentId, $allowedMenuIds, true)) {
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

    public function control($menuId)
    {
        $this->UUID = strtoupper(uniqid());
        $this->UserID = $this->getLoginUser('id') ?? '';
        $this->RouterCode = 'ACCESS-CONTROL';
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', [
            'path' => $this->request->getPath(),
            'menu_id' => $menuId,
        ]);

        $roleCode = (string) ($this->getLoginUser('role') ?? '');
        if ($roleCode === '') {
            return $this->forbidden('User role not found!');
        }

        $role = $this->roleModel->getRoleByCode($roleCode);
        if (!$role) {
            return $this->forbidden('Role not found!');
        }

        $menu = $this->menuModel->getMenuById($menuId);
        if (!$menu) {
            return $this->notFound('Menu not found!');
        }

        $accessRows = $this->roleMenuControlModel
            ->select('menu_control_id')
            ->where('role_id', $role['id'])
            ->where('menu_id', $menuId)
            ->findAll();

        if ($accessRows === []) {
            return $this->success('List access control', []);
        }

        $controlIds = array_values(array_unique(array_map(static fn ($row) => $row['menu_control_id'], $accessRows)));
        $controls = $this->menuControlModel
            ->select('code')
            ->whereIn('id', $controlIds)
            ->findAll();
        $codes = array_values(array_unique(array_map(static fn ($control) => $control['code'], $controls)));
        sort($codes);

        return $this->success('List access control', $codes);
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
}
