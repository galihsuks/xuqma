<?php

namespace App\Models;

class RoleModel extends BaseModel
{
    protected $table            = 'app_roles';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'id',
        'code',
        'name',
        'description',
    ];
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    public function insertRole(array $params)
    {
        $params['id'] = $this->generateIdItem();
        $this->insert($params);
        return $this->getRoleById($params['id']);
    }

    public function getRoleById($id)
    {
        return $this->select('id, code, name, description, created_at, updated_at')->where('id', $id)->first();
    }

    public function getRoleByCode($code)
    {
        return $this->select('id, code, name, description, created_at, updated_at')->where('code', $code)->first();
    }

    public function updateRoleById($id, array $params)
    {
        $updated = $this->update($id, $params);
        if ($updated) {
            return $this->getRoleById($id);
        }
        return null;
    }

    public function destroyRoleById($id)
    {
        $data = $this->getRoleById($id);
        if ($data) {
            $this->delete($id);
            return $data;
        }
        return null;
    }

    public function getRoleListPaginated(int $page, int $pageSize, string $keywords = ''): array
    {
        $offset = max(0, ($page - 1) * $pageSize);

        $builder = $this->db->table('app_roles')
            ->select('id, code, name, description, created_at, updated_at');

        if ($keywords !== '') {
            $builder->groupStart()
                ->like('code', $keywords)
                ->orLike('name', $keywords)
                ->orLike('description', $keywords)
                ->groupEnd();
        }

        $totalItems = (int) $builder->countAllResults(false);
        $items = $builder
            ->orderBy('name', 'ASC')
            ->get($pageSize, $offset)
            ->getResultArray();

        return [
            'items' => $items,
            'total_items' => $totalItems,
        ];
    }

    public function getDropdownOptions(string $keywords = '', ?int $limit = null): array
    {
        $effectiveLimit = $limit ?? (int) $this->LIMIT_DATA;

        $builder = $this->db->table('app_roles')
            ->select("id as value, CONCAT(code, ' - ', name) as label")
            ->orderBy('name', 'ASC')
            ->limit($effectiveLimit);

        if ($keywords !== '') {
            $builder->groupStart()
                ->like('code', $keywords)
                ->orLike('name', $keywords)
                ->groupEnd();
        }

        return $builder->get()->getResultArray();
    }
}
