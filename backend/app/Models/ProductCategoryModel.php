<?php

namespace App\Models;

class ProductCategoryModel extends BaseModel
{
    protected $table = 'product_categories';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id',
        'name',
        'slug',
        'description',
        'icon',
        'display',
        'sort',
    ];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function insertCategory(array $params)
    {
        $params['id'] = $this->generateIdItem();
        $this->insert($params);
        return $this->getCategoryById($params['id']);
    }

    public function getCategoryById(string $id)
    {
        return $this->select('id, name, slug, description, icon, display, sort, created_at, updated_at')
            ->where('id', $id)
            ->first();
    }

    public function getCategoryBySlug(string $slug)
    {
        return $this->select('id, name, slug, description, icon, display, sort, created_at, updated_at')
            ->where('slug', $slug)
            ->first();
    }

    public function updateCategoryById(string $id, array $params)
    {
        $updated = $this->update($id, $params);
        if ($updated) {
            return $this->getCategoryById($id);
        }
        return null;
    }

    public function destroyCategoryById(string $id)
    {
        $data = $this->getCategoryById($id);
        if ($data) {
            $this->delete($id);
            return $data;
        }
        return null;
    }

    public function getCategoryListPaginated(int $page, int $pageSize, string $keywords = ''): array
    {
        $offset = max(0, ($page - 1) * $pageSize);
        $builder = $this->db->table($this->table)
            ->select('id, name, slug, description, icon, display, sort, created_at, updated_at');

        if ($keywords !== '') {
            $builder->groupStart()
                ->like('name', $keywords)
                ->orLike('slug', $keywords)
                ->orLike('description', $keywords)
                ->groupEnd();
        }

        $totalItems = (int) $builder->countAllResults(false);
        $items = $builder
            ->orderBy('sort', 'ASC')
            ->orderBy('name', 'ASC')
            ->get($pageSize, $offset)
            ->getResultArray();

        return [
            'items' => $items,
            'total_items' => $totalItems,
        ];
    }

    public function getVisibleCategories(): array
    {
        return $this->select('id, name, slug, description, icon, display, sort, created_at, updated_at')
            ->where('display', 1)
            ->orderBy('sort', 'ASC')
            ->orderBy('name', 'ASC')
            ->findAll();
    }
}
