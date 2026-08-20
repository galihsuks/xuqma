<?php

namespace App\Models;

class AuthModel extends BaseModel
{
    protected $table            = 'app_users';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    public function getPasswordByUsernameOrEmail($value) {
        return $this->db->table('app_users')
            ->select('id, username, full_name, email, password')
            ->where('username', $value)
            ->orWhere('email', $value)
            ->get()
            ->getRowArray();
    }
    public function getRolesByUserId($userId) {
        return $this->db->table('app_user_roles ur')
            ->select('r.id, r.code, r.name')
            ->join('app_roles r', 'r.id = ur.role_id', 'inner')
            ->where('ur.user_id', $userId)
            ->get()
            ->getRowArray();
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
}
