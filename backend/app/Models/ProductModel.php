<?php

namespace App\Models;

class ProductModel extends BaseModel
{
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id',
        'category_id',
        'sku',
        'name',
        'slug',
        'summary',
        'description',
        'highlight',
        'price',
        'stock',
        'stock_badge',
        'is_featured',
        'display',
        'sort',
    ];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function insertProduct(array $params)
    {
        $params['id'] = $this->generateIdItem();
        $this->insert($params);
        return $this->getProductById($params['id']);
    }

    public function getProductById(string $id)
    {
        return $this->baseSelect()
            ->where('products.id', $id)
            ->first();
    }

    public function getProductBySlug(string $slug)
    {
        return $this->baseSelect()
            ->where('products.slug', $slug)
            ->first();
    }

    public function updateProductById(string $id, array $params)
    {
        $updated = $this->update($id, $params);
        if ($updated) {
            return $this->getProductById($id);
        }
        return null;
    }

    public function destroyProductById(string $id)
    {
        $data = $this->getProductById($id);
        if ($data) {
            $this->delete($id);
            return $data;
        }
        return null;
    }

    public function getProductListPaginated(int $page, int $pageSize, string $keywords = '', ?string $categoryId = null): array
    {
        $offset = max(0, ($page - 1) * $pageSize);
        $builder = $this->db->table($this->table)
            ->select('products.id, products.category_id, product_categories.name as category_name, product_categories.slug as category_slug, products.sku, products.name, products.slug, products.summary, products.highlight, products.price, products.stock, products.stock_badge, products.is_featured, products.display, products.sort, products.created_at, products.updated_at')
            ->join('product_categories', 'product_categories.id = products.category_id', 'inner');

        if ($keywords !== '') {
            $builder->groupStart()
                ->like('products.name', $keywords)
                ->orLike('products.sku', $keywords)
                ->orLike('products.slug', $keywords)
                ->orLike('product_categories.name', $keywords)
                ->groupEnd();
        }

        if ($categoryId !== null && $categoryId !== '') {
            $builder->where('products.category_id', $categoryId);
        }

        $totalItems = (int) $builder->countAllResults(false);
        $items = $builder
            ->orderBy('products.sort', 'ASC')
            ->orderBy('products.name', 'ASC')
            ->get($pageSize, $offset)
            ->getResultArray();

        return [
            'items' => $items,
            'total_items' => $totalItems,
        ];
    }

    public function getVisibleProducts(): array
    {
        return $this->baseSelect()
            ->where('products.display', 1)
            ->orderBy('products.sort', 'ASC')
            ->orderBy('products.name', 'ASC')
            ->findAll();
    }

    public function getFeaturedVisibleProducts(int $limit = 4): array
    {
        return $this->baseSelect()
            ->where('products.display', 1)
            ->where('products.is_featured', 1)
            ->orderBy('products.sort', 'ASC')
            ->orderBy('products.name', 'ASC')
            ->findAll($limit);
    }

    public function getVisibleProductBySlug(string $slug)
    {
        return $this->baseSelect()
            ->where('products.display', 1)
            ->where('products.slug', $slug)
            ->first();
    }

    public function getVisibleProductsByCategorySlug(string $categorySlug): array
    {
        return $this->baseSelect()
            ->where('products.display', 1)
            ->where('product_categories.slug', $categorySlug)
            ->orderBy('products.sort', 'ASC')
            ->orderBy('products.name', 'ASC')
            ->findAll();
    }

    private function baseSelect()
    {
        return $this->select('products.id, products.category_id, product_categories.name as category_name, product_categories.slug as category_slug, products.sku, products.name, products.slug, products.summary, products.description, products.highlight, products.price, products.stock, products.stock_badge, products.is_featured, products.display, products.sort, products.created_at, products.updated_at')
            ->join('product_categories', 'product_categories.id = products.category_id', 'inner');
    }
}
