<?php

namespace App\Controllers;

class RoleMenuControlController extends BaseController
{
    public function index($roleId)
    {
        $this->UUID = strtoupper(uniqid());
        $this->UserID = $this->request->user['id'] ?? '';
        $this->RouterCode = 'ROLE-MENU-CONTROL-INDEX';
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', [
            'path' => $this->request->getPath(),
            'role_id' => $roleId,
        ]);

        $role = $this->roleModel->getRoleById($roleId);
        if (!$role) {
            return $this->notFound('Role not found!');
        }

        $menus = $this->menuModel
            ->select('id, parent_menu_id, name, description, icon, sort, `group`')
            ->orderBy('group', 'ASC')
            ->orderBy('sort', 'ASC')
            ->findAll();

        $menuControls = $this->menuControlModel
            ->select('id, menu_id, name')
            ->findAll();
        $checkedRows = $this->roleMenuControlModel
            ->select('menu_id, menu_control_id')
            ->where('role_id', $roleId)
            ->findAll();

        $checkedMap = [];
        foreach ($checkedRows as $row) {
            $checkedMap[$row['menu_id'] . '|' . $row['menu_control_id']] = true;
        }

        $controlsByMenuId = [];
        foreach ($menuControls as $control) {
            $menuId = $control['menu_id'];
            if (!array_key_exists($menuId, $controlsByMenuId)) {
                $controlsByMenuId[$menuId] = [];
            }
            $controlsByMenuId[$menuId][] = [
                'id' => $control['id'],
                'name' => $control['name'],
                'checked' => isset($checkedMap[$menuId . '|' . $control['id']]),
            ];
        }

        foreach ($controlsByMenuId as &$controls) {
            usort($controls, static fn ($a, $b) => strcmp($a['name'], $b['name']));
        }

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

        $rootMenus = $menuByParent[null] ?? [];
        usort($rootMenus, [$this, 'sortMenuItems']);

        $data = array_map(
            fn ($menu) => $this->buildRoleMenuTreeNode($menu, $menuByParent, $controlsByMenuId),
            $rootMenus,
        );

        return $this->success('Role menu control list for role ' . $role['name'], $data);
    }

    public function create()
    {
        $this->UUID = strtoupper(uniqid());
        $this->UserID = $this->request->user['id'] ?? '';
        $this->RouterCode = 'ROLE-MENU-CONTROL-CREATE';
        $body = $this->getBody();
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', [
            'path' => $this->request->getPath(),
            'request' => $body,
        ]);

        $roleId = (string) ($body['role_id'] ?? '');
        $changes = $body['data'] ?? null;

        if ($roleId === '') {
            return $this->badRequest('role_id is required');
        }
        if (!is_array($changes)) {
            return $this->badRequest('data must be an array');
        }

        $role = $this->roleModel->getRoleById($roleId);
        if (!$role) {
            return $this->notFound('Role not found!');
        }

        try {
            $inserted = 0;
            $deleted = 0;

            foreach ($changes as $index => $item) {
                if (!is_array($item)) {
                    return $this->badRequest("data.$index must be an object");
                }

                $menuId = (string) ($item['menu_id'] ?? '');
                $menuControlId = (string) ($item['menu_control_id'] ?? '');
                $value = $item['value'] ?? null;

                if ($menuId === '' || $menuControlId === '') {
                    return $this->badRequest("data.$index menu_id and menu_control_id are required");
                }
                if (!is_bool($value)) {
                    return $this->badRequest("data.$index value must be boolean");
                }

                $menu = $this->menuModel->getMenuById($menuId);
                if (!$menu) {
                    return $this->badRequest("data.$index menu_id is invalid");
                }

                $menuControl = $this->menuControlModel->getMenuControlById($menuControlId);
                if (!$menuControl) {
                    return $this->badRequest("data.$index menu_control_id is invalid");
                }
                if ((string) $menuControl['menu_id'] !== $menuId) {
                    return $this->badRequest("data.$index menu_control_id is not part of selected menu_id");
                }

                $existing = $this->roleMenuControlModel->getByUniqueKey($roleId, $menuId, $menuControlId);
                if ($value === true) {
                    if (!$existing) {
                        $this->roleMenuControlModel->insertRoleMenuControl([
                            'role_id' => $roleId,
                            'menu_id' => $menuId,
                            'menu_control_id' => $menuControlId,
                        ]);
                        $inserted++;
                    }
                } else {
                    if ($existing) {
                        $this->roleMenuControlModel->destroyByUniqueKey($roleId, $menuId, $menuControlId);
                        $deleted++;
                    }
                }
            }

            return $this->success('Role menu control updated successfully', [
                'inserted' => $inserted,
                'deleted' => $deleted,
            ]);
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

    private function buildRoleMenuTreeNode(array $menu, array $menuByParent, array $controlsByMenuId): array
    {
        $result = [
            'menu_id' => $menu['id'],
            'menu_name' => $menu['name'],
            'menu_description' => $menu['description'],
            'menu_icon' => $menu['icon'],
            'menu_access' => $controlsByMenuId[$menu['id']] ?? [],
        ];

        $children = $menuByParent[$menu['id']] ?? [];
        if ($children !== []) {
            usort($children, [$this, 'sortMenuItems']);
            $result['menu_chilren'] = array_map(
                fn ($child) => $this->buildRoleMenuTreeNode($child, $menuByParent, $controlsByMenuId),
                $children,
            );
        }

        return $result;
    }
}
