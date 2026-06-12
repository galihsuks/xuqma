<?php

namespace App\Models;

class MenuControlModel extends BaseModel
{
    protected $table            = 'app_menu_controls';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'id',
        'menu_id',
        'code',
        'name',
    ];
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    public function insertMenuControl(array $params)
    {
        $params['id'] = $this->generateIdItem();
        $this->insert($params);
        return $this->getMenuControlById($params['id']);
    }

    public function getMenuControlById($id)
    {
        return $this->select('id, menu_id, code, name, created_at, updated_at')->where('id', $id)->first();
    }

    public function getByMenuAndCode($menuId, $code)
    {
        return $this->select('id, menu_id, code, name, created_at, updated_at')->where('menu_id', $menuId)->where('code', $code)->first();
    }

    public function updateMenuControlById($id, array $params)
    {
        $updated = $this->update($id, $params);
        if ($updated) {
            return $this->getMenuControlById($id);
        }
        return null;
    }

    public function destroyMenuControlById($id)
    {
        $data = $this->getMenuControlById($id);
        if ($data) {
            $this->delete($id);
            return $data;
        }
        return null;
    }
}
