<?php

namespace App\Models;

class AppSupportModel extends BaseModel
{
    protected $table            = 'app_support';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'key', 
        'value',
        'datatype'
    ];
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    public function insertAppSupport($params) {
        return $this->insert($params);
    }
    public function destroyAppSupportById($id) {
        $data = $this->getAppSupportById($id);
        if ($data) {
            $this->delete($id);
            return $data;
        }
        return null;
    }
    public function getAppSupportById($id = null) {
        if ($id) {
            return $this->select('id, `key`, value, datatype, created_at, updated_at')->where('id', $id)->first();
        }
        return $this->select('id, `key`, value, datatype, created_at, updated_at')->findAll();
    }
    public function getAppSupportByKey($key) {
        return $this->select('id, `key`, value, datatype, created_at, updated_at')->where('key', $key)->first();
    }
    public function getValue($key) {
        $app_support = $this->select('datatype, value')->where('key', $key)->first();
        if ($app_support) {
            switch ($app_support['datatype']) {
                case 'number':
                    return (float)$app_support['value'];
                case 'json':
                    return json_decode($app_support['value'], true);
                case 'boolean':
                    return $app_support['value'] == '1' ? true : false;
                default:
                    return $app_support['value'];
            }
        }
        return null;    
    }
    public function updateAppSupportById($id, $params) {
        $updated = $this->update($id, $params);
        if ($updated) {
            return $this->getAppSupportById($id);
        }
        return null;
    }

    public function getAppSupportListPaginated(int $page, int $pageSize, string $keywords = ''): array
    {
        $offset = max(0, ($page - 1) * $pageSize);

        $builder = $this->db->table('app_support')
            ->select('id, `key`, value, datatype, created_at, updated_at');

        if ($keywords !== '') {
            $builder->groupStart()
                ->like('key', $keywords)
                ->orLike('value', $keywords)
                ->orLike('datatype', $keywords)
                ->groupEnd();
        }

        $totalItems = (int) $builder->countAllResults(false);
        $items = $builder
            ->orderBy('created_at', 'DESC')
            ->get($pageSize, $offset)
            ->getResultArray();

        return [
            'items' => $items,
            'total_items' => $totalItems,
        ];
    }
}
