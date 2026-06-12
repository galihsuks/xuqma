<?php

namespace App\Database\Seeds;

use App\Models\MenuControlModel;
use App\Models\MenuModel;
use CodeIgniter\Database\Seeder;

class BaseAppMenuSeeder extends Seeder
{
    public function run()
    {
        $menuModel = new MenuModel();
        $menuControlModel = new MenuControlModel();

        $menus = [
            [
                'name' => 'Dashboard',
                'description' => 'Main dashboard page.',
                'url' => '/dashboard',
                'group' => 'main',
                'icon' => 'LayoutDashboard',
                'display' => 1,
                'sort' => 1,
                'controls' => [
                    ['code' => 'R', 'name' => 'Read'],
                ],
            ],
            [
                'name' => 'Menu',
                'description' => 'Manage application menus and controls.',
                'url' => '/system/menu',
                'group' => 'system',
                'icon' => 'FolderTree',
                'display' => 1,
                'sort' => 1,
                'controls' => [
                    ['code' => 'C', 'name' => 'Create'],
                    ['code' => 'R', 'name' => 'Read'],
                    ['code' => 'U', 'name' => 'Update'],
                    ['code' => 'D', 'name' => 'Delete'],
                ],
            ],
            [
                'name' => 'Role',
                'description' => 'Manage roles and role access.',
                'url' => '/system/role',
                'group' => 'system',
                'icon' => 'Shield',
                'display' => 1,
                'sort' => 2,
                'controls' => [
                    ['code' => 'C', 'name' => 'Create'],
                    ['code' => 'R', 'name' => 'Read'],
                    ['code' => 'U', 'name' => 'Update'],
                    ['code' => 'D', 'name' => 'Delete'],
                    ['code' => 'AC', 'name' => 'Access Control'],
                ],
            ],
            [
                'name' => 'User',
                'description' => 'Manage application user accounts.',
                'url' => '/system/user',
                'group' => 'system',
                'icon' => 'Users',
                'display' => 1,
                'sort' => 3,
                'controls' => [
                    ['code' => 'C', 'name' => 'Create'],
                    ['code' => 'R', 'name' => 'Read'],
                    ['code' => 'U', 'name' => 'Update'],
                    ['code' => 'D', 'name' => 'Delete'],
                ],
            ],
            [
                'name' => 'Parameter',
                'description' => 'Manage application parameters.',
                'url' => '/system/parameter',
                'group' => 'system',
                'icon' => 'SlidersHorizontal',
                'display' => 1,
                'sort' => 4,
                'controls' => [
                    ['code' => 'C', 'name' => 'Create'],
                    ['code' => 'R', 'name' => 'Read'],
                    ['code' => 'U', 'name' => 'Update'],
                    ['code' => 'D', 'name' => 'Delete'],
                ],
            ],
            [
                'name' => 'Log',
                'description' => 'Monitor frontend and backend logs.',
                'url' => '/system/log',
                'group' => 'system',
                'icon' => 'Logs',
                'display' => 1,
                'sort' => 5,
                'controls' => [
                    ['code' => 'R', 'name' => 'Read'],
                    ['code' => 'D', 'name' => 'Delete'],
                ],
            ],
        ];

        foreach ($menus as $menu) {
            $savedMenu = $this->upsertMenu($menuModel, $menu);

            foreach ($menu['controls'] as $control) {
                $this->upsertMenuControl($menuControlModel, (string) $savedMenu['id'], $control);
            }
        }
    }

    private function upsertMenu(MenuModel $menuModel, array $menu): array
    {
        $existing = $this->db->table('app_menus')
            ->select('id, parent_menu_id, name, description, url, `group`, icon, display, sort, created_at, updated_at')
            ->where('url', $menu['url'])
            ->get()
            ->getRowArray();

        $payload = [
            'parent_menu_id' => null,
            'name' => $menu['name'],
            'description' => $menu['description'],
            'url' => $menu['url'],
            'group' => $menu['group'],
            'icon' => $menu['icon'],
            'display' => $menu['display'],
            'sort' => $menu['sort'],
        ];

        if ($existing) {
            return $menuModel->updateMenuById($existing['id'], $payload) ?? $existing;
        }

        return $menuModel->insertMenu($payload);
    }

    private function upsertMenuControl(MenuControlModel $menuControlModel, string $menuId, array $control): void
    {
        $existing = $menuControlModel->getByMenuAndCode($menuId, $control['code']);
        $payload = [
            'menu_id' => $menuId,
            'code' => $control['code'],
            'name' => $control['name'],
        ];

        if ($existing) {
            $menuControlModel->updateMenuControlById($existing['id'], $payload);
            return;
        }

        $menuControlModel->insertMenuControl($payload);
    }
}
