<?php

namespace App\Database\Seeds;

use App\Models\MenuControlModel;
use App\Models\MenuModel;
use CodeIgniter\Database\Seeder;

class EcommerceMenuSeeder extends Seeder
{
    public function run()
    {
        $menuModel = new MenuModel();
        $menuControlModel = new MenuControlModel();

        $menus = [
            [
                'name' => 'Products',
                'description' => 'Manage storefront products and catalog details.',
                'url' => '/catalog/products',
                'group' => 'main',
                'icon' => 'Boxes',
                'display' => 1,
                'sort' => 2,
                'controls' => [
                    ['code' => 'C', 'name' => 'Create'],
                    ['code' => 'R', 'name' => 'Read'],
                    ['code' => 'U', 'name' => 'Update'],
                    ['code' => 'D', 'name' => 'Delete'],
                ],
            ],
            [
                'name' => 'Orders',
                'description' => 'Manage customer orders and fulfillment.',
                'url' => '/sales/orders',
                'group' => 'main',
                'icon' => 'PackageSearch',
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
                'name' => 'Articles',
                'description' => 'Manage SEO article content for storefront.',
                'url' => '/content/articles',
                'group' => 'main',
                'icon' => 'FilePenLine',
                'display' => 1,
                'sort' => 4,
                'controls' => [
                    ['code' => 'C', 'name' => 'Create'],
                    ['code' => 'R', 'name' => 'Read'],
                    ['code' => 'U', 'name' => 'Update'],
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
