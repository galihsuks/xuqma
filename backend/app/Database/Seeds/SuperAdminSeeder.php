<?php

namespace App\Database\Seeds;

use App\Models\RoleModel;
use App\Models\RoleMenuControlModel;
use App\Models\UserModel;
use App\Models\UserRoleModel;
use CodeIgniter\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    public function run()
    {
        $userModel = new UserModel();
        $roleModel = new RoleModel();
        $userRoleModel = new UserRoleModel();
        $roleMenuControlModel = new RoleMenuControlModel();

        $role = $roleModel->getRoleByCode('SA');
        if (!$role) {
            $this->call('RoleSeeder');
            $role = $roleModel->getRoleByCode('SA');
        }

        $this->call('BaseAppMenuSeeder');

        if (!$role) {
            return;
        }

        $username = 'superadmin';
        $email = 'superadmin@local.test';
        $fullName = 'Super Admin';
        $plainPassword = 'SuperAdmin123!';

        $user = $userModel->getUserByEmailOrUsername($email, $username);
        if ($user) {
            $userModel->updateUserById($user['id'], [
                'username' => $username,
                'full_name' => $fullName,
                'email' => $email,
                'password' => password_hash($plainPassword, PASSWORD_BCRYPT),
            ]);
            $userId = $user['id'];
        } else {
            $created = $userModel->insertUser([
                'username' => $username,
                'full_name' => $fullName,
                'email' => $email,
                'password' => password_hash($plainPassword, PASSWORD_BCRYPT),
            ]);
            $userId = $created['id'];
        }

        $userRoleModel->replaceByUserId($userId, (string) $role['id']);

        $menuControls = $this->db->table('app_menu_controls')
            ->select('id, menu_id, code, name, created_at, updated_at')
            ->orderBy('menu_id', 'ASC')
            ->orderBy('code', 'ASC')
            ->get()
            ->getResultArray();

        foreach ($menuControls as $menuControl) {
            $existingAccess = $roleMenuControlModel->getByUniqueKey(
                (string) $role['id'],
                (string) $menuControl['menu_id'],
                (string) $menuControl['id']
            );

            if ($existingAccess) {
                continue;
            }

            $roleMenuControlModel->insertRoleMenuControl([
                'role_id' => (string) $role['id'],
                'menu_id' => (string) $menuControl['menu_id'],
                'menu_control_id' => (string) $menuControl['id'],
            ]);
        }
    }
}
