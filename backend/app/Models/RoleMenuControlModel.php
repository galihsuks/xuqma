<?php

namespace App\Models;

class RoleMenuControlModel extends BaseModel
{
    protected $table            = 'app_role_menu_controls';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'id',
        'role_id',
        'menu_id',
        'menu_control_id',
    ];
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    public function insertRoleMenuControl(array $params)
    {
        $params['id'] = $this->generateIdItem();
        $this->insert($params);
        return $this->getRoleMenuControlById($params['id']);
    }

    public function getRoleMenuControlById($id)
    {
        return $this->select('id, role_id, menu_id, menu_control_id, created_at, updated_at')->where('id', $id)->first();
    }

    public function getByUniqueKey($roleId, $menuId, $menuControlId)
    {
        return $this->where('role_id', $roleId)
            ->where('menu_id', $menuId)
            ->where('menu_control_id', $menuControlId)
            ->select('id, role_id, menu_id, menu_control_id, created_at, updated_at')
            ->first();
    }

    public function updateRoleMenuControlById($id, array $params)
    {
        $updated = $this->update($id, $params);
        if ($updated) {
            return $this->getRoleMenuControlById($id);
        }
        return null;
    }

    public function destroyRoleMenuControlById($id)
    {
        $data = $this->getRoleMenuControlById($id);
        if ($data) {
            $this->delete($id);
            return $data;
        }
        return null;
    }

    public function destroyByUniqueKey($roleId, $menuId, $menuControlId): void
    {
        $this->where('role_id', $roleId)
            ->where('menu_id', $menuId)
            ->where('menu_control_id', $menuControlId)
            ->delete();
    }
}
