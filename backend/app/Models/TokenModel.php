<?php

namespace App\Models;

class TokenModel extends BaseModel
{
    protected $table            = 'app_tokens';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'id',
        'user_id', 
        'token', 
        'expired_time', 
        'ip', 
        'device', 
        'platform', 
        'created_at'
    ];
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    public function insertToken($params) {
        $params['id'] = $this->generateIdItem();
        $params['created_at'] = date('Y-m-d H:i:s');
        return $this->insert($params);
    }
    public function getTokenByUserId($user_id) {
        return $this
            ->select('id, user_id, token, expired_time, ip, device, platform, created_at, updated_at')
            ->where('user_id', $user_id)
            ->findAll();
    }
    public function getTokenByToken($token) {
        return $this
            ->select('id, user_id, token, expired_time, ip, device, platform, created_at, updated_at')
            ->where('app_tokens.token', $token)
            ->first();
    }
    public function destroyTokenByToken($token) {
        return $this->where('token', $token)->delete();
    }
    public function destroyTokenByUserId($user_id) {
        return $this->where('user_id', $user_id)->delete();
    }
}
