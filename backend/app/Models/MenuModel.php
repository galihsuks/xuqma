<?php

namespace App\Models;

class MenuModel extends BaseModel
{
    protected $table            = 'app_menus';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'id',
        'parent_menu_id',
        'name',
        'description',
        'url',
        'group',
        'icon',
        'display',
        'sort',
    ];
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    public function insertMenu(array $params)
    {
        $params['id'] = $this->generateIdItem();
        $this->insert($params);
        return $this->getMenuById($params['id']);
    }

    public function getMenuById($id)
    {
        return $this->select('id, parent_menu_id, name, description, url, `group`, icon, display, sort, created_at, updated_at')->where('id', $id)->first();
    }

    public function updateMenuById($id, array $params)
    {
        $updated = $this->update($id, $params);
        if ($updated) {
            return $this->getMenuById($id);
        }
        return null;
    }

    public function destroyMenuById($id)
    {
        $data = $this->getMenuById($id);
        if ($data) {
            $this->delete($id);
            return $data;
        }
        return null;
    }
}
