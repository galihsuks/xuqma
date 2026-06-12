<?php

namespace App\Models;

class UserModel extends BaseModel
{
    protected $table            = 'app_users';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'id',
        'username', 
        'full_name', 
        'email', 
        'password',
    ];
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    public function insertUser($params) {
        $params['id'] = $this->generateIdItem();
        $this->insert($params);
        return $this->getUserByUsername($params['username']);
    }
    public function getUserByUsername($username) {
        return $this->select('id, username, full_name, email, password, created_at, updated_at')->where('username', $username)->first();
    }
    public function getUserById($id) {
        return $this->select('id, username, full_name, email, password, created_at, updated_at')->where('id', $id)->first();
    }
    public function getUserByEmailOrUsername($email, $username) {
        return $this->select('id, username, full_name, email, password, created_at, updated_at')->where('email', $email)->orWhere('username', $username)->first();
    }
    public function updateUserById($id, $params) {
        $updated = $this->update($id, $params);
        if ($updated) {
            return $this->getUserById($id);
        }
        return null;
    }
    public function destroyUserById($id) {
        $data = $this->getUserById($id);
        if ($data) {
            $this->delete($id);
            return $data;
        }
        return null;
    }

    public function getUserListPaginated(int $page, int $pageSize, string $keywords = '', string $roleId = ''): array
    {
        $offset = max(0, ($page - 1) * $pageSize);

        $builder = $this->db->table('app_users u')
            ->select('u.id, u.username, u.full_name, u.email, u.created_at, u.updated_at, r.name as role_name')
            ->join('app_user_roles ur', 'ur.user_id = u.id', 'left')
            ->join('app_roles r', 'r.id = ur.role_id', 'left');

        if ($keywords !== '') {
            $builder->groupStart()
                ->like('u.username', $keywords)
                ->orLike('u.full_name', $keywords)
                ->orLike('u.email', $keywords)
                ->orLike('r.name', $keywords)
                ->groupEnd();
        }

        if ($roleId !== '') {
            $builder->where('ur.role_id', $roleId);
        }

        $totalItems = (int) $builder->countAllResults(false);

        $rows = $builder
            ->orderBy('u.full_name', 'ASC')
            ->limit($pageSize, $offset)
            ->get()
            ->getResultArray();

        return [
            'items' => $rows,
            'total_items' => $totalItems,
        ];
    }

    public function getDropdownOptions(string $keywords = '', ?int $limit = null): array
    {
        $effectiveLimit = $limit ?? (int) $this->LIMIT_DATA;

        $builder = $this->db->table('app_users')
            ->select("id as value, CONCAT(full_name, ' (@', username, ')') as label")
            ->orderBy('full_name', 'ASC')
            ->limit($effectiveLimit);

        if ($keywords !== '') {
            $builder->groupStart()
                ->like('full_name', $keywords)
                ->orLike('username', $keywords)
                ->orLike('email', $keywords)
                ->groupEnd();
        }

        return $builder->get()->getResultArray();
    }
}
