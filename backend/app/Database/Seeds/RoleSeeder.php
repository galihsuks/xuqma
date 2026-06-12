<?php

namespace App\Database\Seeds;

use App\Models\RoleModel;
use CodeIgniter\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roleModel = new RoleModel();

        $roles = [
            [
                'code' => 'SA',
                'name' => 'Super Admin',
                'description' => 'Akses penuh ke seluruh fitur sistem.',
            ],
            [
                'code' => 'A',
                'name' => 'Admin',
                'description' => 'Akses manajemen operasional aplikasi.',
            ],
            [
                'code' => 'U',
                'name' => 'User',
                'description' => 'Pengguna umum aplikasi.',
            ],
            [
                'code' => 'V',
                'name' => 'Viewer',
                'description' => 'Akses baca saja (read-only).',
            ],
        ];

        foreach ($roles as $role) {
            $existing = $roleModel->getRoleByCode($role['code']);
            if ($existing) {
                $roleModel->updateRoleById($existing['id'], [
                    'name' => $role['name'],
                    'description' => $role['description'],
                ]);
                continue;
            }

            $roleModel->insertRole($role);
        }
    }
}
