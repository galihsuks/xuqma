<?php

namespace App\Controllers;

class ProductCategoryController extends BaseController
{
    public function index()
    {
        $this->UUID = strtoupper(uniqid());
        $this->UserID = $this->request->user['id'] ?? '';
        $this->RouterCode = 'PRODUCT-CATEGORY-INDEX';
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $defaultPageSize = (int) $this->productCategoryModel->LIMIT_DATA;
        $pageSize = max(1, min(100, (int) ($this->request->getGet('page_size') ?? $defaultPageSize)));
        $keywords = trim((string) ($this->request->getGet('keywords') ?? ''));
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', [
            'path' => $this->request->getPath(),
            'query' => $this->request->getGet(),
        ]);

        $result = $this->productCategoryModel->getCategoryListPaginated($page, $pageSize, $keywords);
        $items = $result['items'];
        $totalItems = (int) $result['total_items'];
        $totalPages = max(1, (int) ceil($totalItems / $pageSize));

        return $this->success('List product category', $items, [
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
        $this->RouterCode = 'PRODUCT-CATEGORY-DETAIL';
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', ['path' => $this->request->getPath()]);

        $data = $this->productCategoryModel->getCategoryById((string) $id);
        if (!$data) {
            return $this->notFound('Product category not found!');
        }

        return $this->success('Detail product category', $data);
    }

    public function create()
    {
        $this->UUID = strtoupper(uniqid());
        $this->UserID = $this->request->user['id'] ?? '';
        $this->RouterCode = 'PRODUCT-CATEGORY-CREATE';
        $body = $this->getBody();
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', ['path' => $this->request->getPath(), 'request' => $body]);

        $rules = [
            'name' => 'required|min_length[2]|max_length[120]',
            'slug' => 'required|min_length[2]|max_length[160]',
            'description' => 'permit_empty',
            'icon' => 'permit_empty|max_length[100]',
            'display' => 'required|in_list[0,1]',
            'sort' => 'required|integer',
        ];
        if (!$this->validateData($body, $rules)) {
            return $this->validationError($this->validator->getErrors());
        }

        if ($this->productCategoryModel->getCategoryBySlug($body['slug'])) {
            return $this->badRequest('Slug already used!');
        }

        try {
            $created = $this->productCategoryModel->insertCategory([
                'name' => $body['name'],
                'slug' => $body['slug'],
                'description' => $body['description'] ?? null,
                'icon' => $body['icon'] ?? null,
                'display' => filter_var($body['display'], FILTER_VALIDATE_BOOLEAN),
                'sort' => (int) $body['sort'],
            ]);

            return $this->created('Product category created successfully', $created);
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
        $this->RouterCode = 'PRODUCT-CATEGORY-EDIT';
        $body = $this->getBody();
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', ['path' => $this->request->getPath(), 'request' => $body]);

        $current = $this->productCategoryModel->getCategoryById((string) $id);
        if (!$current) {
            return $this->notFound('Product category not found!');
        }

        $rules = [
            'name' => 'required|min_length[2]|max_length[120]',
            'slug' => 'required|min_length[2]|max_length[160]',
            'description' => 'permit_empty',
            'icon' => 'permit_empty|max_length[100]',
            'display' => 'required|in_list[0,1]',
            'sort' => 'required|integer',
        ];
        if (!$this->validateData($body, $rules)) {
            return $this->validationError($this->validator->getErrors());
        }

        $duplicate = $this->productCategoryModel->getCategoryBySlug($body['slug']);
        if ($duplicate && (string) $duplicate['id'] !== (string) $id) {
            return $this->badRequest('Slug already used!');
        }

        try {
            $updated = $this->productCategoryModel->updateCategoryById((string) $id, [
                'name' => $body['name'],
                'slug' => $body['slug'],
                'description' => $body['description'] ?? null,
                'icon' => $body['icon'] ?? null,
                'display' => filter_var($body['display'], FILTER_VALIDATE_BOOLEAN),
                'sort' => (int) $body['sort'],
            ]);

            return $this->success('Product category updated successfully', $updated);
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
        $this->RouterCode = 'PRODUCT-CATEGORY-DESTROY';
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', ['path' => $this->request->getPath()]);

        if ($this->productModel->where('category_id', $id)->countAllResults() > 0) {
            return $this->badRequest('Category is still used by products!');
        }

        try {
            $deleted = $this->productCategoryModel->destroyCategoryById((string) $id);
            if (!$deleted) {
                return $this->notFound('Product category not found!');
            }

            return $this->success('Product category deleted successfully', $deleted);
        } catch (\Throwable $th) {
            return $this->serverError("There's a problem with the server, Contact us!", [
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
            ]);
        }
    }
}
