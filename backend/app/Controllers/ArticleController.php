<?php

namespace App\Controllers;

class ArticleController extends BaseController
{
    public function index()
    {
        $this->UUID = strtoupper(uniqid());
        $this->UserID = $this->request->user['id'] ?? '';
        $this->RouterCode = 'ARTICLE-INDEX';
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $defaultPageSize = (int) $this->articleModel->LIMIT_DATA;
        $pageSize = max(1, min(100, (int) ($this->request->getGet('page_size') ?? $defaultPageSize)));
        $keywords = trim((string) ($this->request->getGet('keywords') ?? ''));
        $status = trim((string) ($this->request->getGet('status') ?? ''));
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', [
            'path' => $this->request->getPath(),
            'query' => $this->request->getGet(),
        ]);

        $result = $this->articleModel->getArticleListPaginated($page, $pageSize, $keywords, $status);
        $items = $result['items'];
        $totalItems = (int) $result['total_items'];
        $totalPages = max(1, (int) ceil($totalItems / $pageSize));

        return $this->success('List article', $items, [
            'page' => $page,
            'page_size' => $pageSize,
            'total_items' => $totalItems,
            'total_pages' => $totalPages,
            'has_next' => $page < $totalPages,
            'has_prev' => $page > 1,
        ]);
    }

    public function detail($id)
    {
        $this->UUID = strtoupper(uniqid());
        $this->UserID = $this->request->user['id'] ?? '';
        $this->RouterCode = 'ARTICLE-DETAIL';
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', ['path' => $this->request->getPath()]);

        $data = $this->articleModel->getArticleById((string) $id);
        if (!$data) {
            return $this->notFound('Article not found!');
        }

        return $this->success('Detail article', $data);
    }

    public function create()
    {
        $this->UUID = strtoupper(uniqid());
        $this->UserID = $this->request->user['id'] ?? '';
        $this->RouterCode = 'ARTICLE-CREATE';
        $body = $this->getBody();
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', ['path' => $this->request->getPath(), 'request' => $body]);

        if (!$this->validateData($body, $this->articleRules())) {
            return $this->validationError($this->validator->getErrors());
        }

        if ($this->articleModel->getArticleBySlug($body['slug'])) {
            return $this->badRequest('Slug already used!');
        }

        try {
            $created = $this->articleModel->insertArticle($this->mapArticlePayload($body));
            return $this->created('Article created successfully', $created);
        } catch (\Throwable $th) {
            return $this->serverError("There's a problem with the server, Contact us!", [
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
            ]);
        }
    }

    public function edit($id)
    {
        $this->UUID = strtoupper(uniqid());
        $this->UserID = $this->request->user['id'] ?? '';
        $this->RouterCode = 'ARTICLE-EDIT';
        $body = $this->getBody();
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', ['path' => $this->request->getPath(), 'request' => $body]);

        $current = $this->articleModel->getArticleById((string) $id);
        if (!$current) {
            return $this->notFound('Article not found!');
        }

        if (!$this->validateData($body, $this->articleRules())) {
            return $this->validationError($this->validator->getErrors());
        }

        $duplicate = $this->articleModel->getArticleBySlug($body['slug']);
        if ($duplicate && (string) $duplicate['id'] !== (string) $id) {
            return $this->badRequest('Slug already used!');
        }

        try {
            $updated = $this->articleModel->updateArticleById((string) $id, $this->mapArticlePayload($body));
            return $this->success('Article updated successfully', $updated);
        } catch (\Throwable $th) {
            return $this->serverError("There's a problem with the server, Contact us!", [
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
            ]);
        }
    }

    public function destroy($id)
    {
        $this->UUID = strtoupper(uniqid());
        $this->UserID = $this->request->user['id'] ?? '';
        $this->RouterCode = 'ARTICLE-DESTROY';
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', ['path' => $this->request->getPath()]);

        try {
            $deleted = $this->articleModel->destroyArticleById((string) $id);
            if (!$deleted) {
                return $this->notFound('Article not found!');
            }

            return $this->success('Article deleted successfully', $deleted);
        } catch (\Throwable $th) {
            return $this->serverError("There's a problem with the server, Contact us!", [
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
            ]);
        }
    }

    private function articleRules(): array
    {
        return [
            'title' => 'required|min_length[2]|max_length[180]',
            'slug' => 'required|min_length[2]|max_length[220]',
            'category' => 'required|min_length[2]|max_length[100]',
            'excerpt' => 'permit_empty',
            'content' => 'permit_empty',
            'author_name' => 'permit_empty|max_length[120]',
            'status' => 'required|in_list[draft,published]',
            'published_at' => 'permit_empty',
            'read_time' => 'permit_empty|max_length[50]',
        ];
    }

    private function mapArticlePayload(array $body): array
    {
        return [
            'title' => $body['title'],
            'slug' => $body['slug'],
            'category' => $body['category'],
            'excerpt' => $body['excerpt'] ?? null,
            'content' => $body['content'] ?? null,
            'author_name' => $body['author_name'] ?? null,
            'status' => $body['status'],
            'published_at' => ($body['published_at'] ?? '') !== '' ? $body['published_at'] : null,
            'read_time' => $body['read_time'] ?? null,
        ];
    }
}
