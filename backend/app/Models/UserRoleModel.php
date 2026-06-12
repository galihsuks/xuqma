<?php

namespace App\Models;

class UserRoleModel extends BaseModel
{
    protected $table            = 'app_user_roles';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'id',
        'user_id',
        'role_id',
    ];
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    public function getRoleIdsByUserId($userId): array
    {
        $rows = $this->select('role_id')->where('user_id', $userId)->findAll();
        return array_values(array_map(static fn ($row) => $row['role_id'], $rows));
    }

    public function replaceByUserId($userId, string $roleId): void
    {
        $this->where('user_id', $userId)->delete();

        $this->insert([
            'id' => $this->generateIdItem(),
            'user_id' => $userId,
            'role_id' => $roleId,
        ]);
    }
}
