<?php

namespace App\Models;

class ArticleModel extends BaseModel
{
    protected $table = 'articles';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id',
        'title',
        'slug',
        'category',
        'excerpt',
        'content',
        'author_name',
        'status',
        'published_at',
        'read_time',
    ];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function insertArticle(array $params)
    {
        $params['id'] = $this->generateIdItem();
        $this->insert($params);
        return $this->getArticleById($params['id']);
    }

    public function getArticleById(string $id)
    {
        return $this->select('id, title, slug, category, excerpt, content, author_name, status, published_at, read_time, created_at, updated_at')
            ->where('id', $id)
            ->first();
    }

    public function getArticleBySlug(string $slug)
    {
        return $this->select('id, title, slug, category, excerpt, content, author_name, status, published_at, read_time, created_at, updated_at')
            ->where('slug', $slug)
            ->first();
    }

    public function updateArticleById(string $id, array $params)
    {
        $updated = $this->update($id, $params);
        if ($updated) {
            return $this->getArticleById($id);
        }
        return null;
    }

    public function destroyArticleById(string $id)
    {
        $data = $this->getArticleById($id);
        if ($data) {
            $this->delete($id);
            return $data;
        }
        return null;
    }

    public function getArticleListPaginated(int $page, int $pageSize, string $keywords = '', string $status = ''): array
    {
        $offset = max(0, ($page - 1) * $pageSize);
        $builder = $this->db->table($this->table)
            ->select('id, title, slug, category, excerpt, author_name, status, published_at, read_time, created_at, updated_at');

        if ($keywords !== '') {
            $builder->groupStart()
                ->like('title', $keywords)
                ->orLike('slug', $keywords)
                ->orLike('category', $keywords)
                ->orLike('author_name', $keywords)
                ->groupEnd();
        }

        if ($status !== '') {
            $builder->where('status', $status);
        }

        $totalItems = (int) $builder->countAllResults(false);
        $items = $builder
            ->orderBy('published_at', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->get($pageSize, $offset)
            ->getResultArray();

        return [
            'items' => $items,
            'total_items' => $totalItems,
        ];
    }

    public function getPublishedArticles(int $limit = 0): array
    {
        $builder = $this->select('id, title, slug, category, excerpt, content, author_name, status, published_at, read_time, created_at, updated_at')
            ->where('status', 'published')
            ->orderBy('published_at', 'DESC')
            ->orderBy('created_at', 'DESC');

        if ($limit > 0) {
            return $builder->findAll($limit);
        }

        return $builder->findAll();
    }

    public function getPublishedArticleBySlug(string $slug)
    {
        return $this->select('id, title, slug, category, excerpt, content, author_name, status, published_at, read_time, created_at, updated_at')
            ->where('status', 'published')
            ->where('slug', $slug)
            ->first();
    }
}
