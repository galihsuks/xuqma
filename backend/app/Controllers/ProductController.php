<?php

namespace App\Controllers;

class ProductController extends BaseController
{
    public function index()
    {
        $this->UUID = strtoupper(uniqid());
        $this->UserID = $this->request->user['id'] ?? '';
        $this->RouterCode = 'PRODUCT-INDEX';
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $defaultPageSize = (int) $this->productModel->LIMIT_DATA;
        $pageSize = max(1, min(100, (int) ($this->request->getGet('page_size') ?? $defaultPageSize)));
        $keywords = trim((string) ($this->request->getGet('keywords') ?? ''));
        $categoryId = trim((string) ($this->request->getGet('category_id') ?? ''));
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', [
            'path' => $this->request->getPath(),
            'query' => $this->request->getGet(),
        ]);

        $result = $this->productModel->getProductListPaginated($page, $pageSize, $keywords, $categoryId !== '' ? $categoryId : null);
        $items = $result['items'];
        $totalItems = (int) $result['total_items'];
        $totalPages = max(1, (int) ceil($totalItems / $pageSize));

        return $this->success('List product', $items, [
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
        $this->RouterCode = 'PRODUCT-DETAIL';
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', ['path' => $this->request->getPath()]);

        $data = $this->productModel->getProductById((string) $id);
        if (!$data) {
            return $this->notFound('Product not found!');
        }

        return $this->success('Detail product', $data);
    }

    public function create()
    {
        $this->UUID = strtoupper(uniqid());
        $this->UserID = $this->request->user['id'] ?? '';
        $this->RouterCode = 'PRODUCT-CREATE';
        $body = $this->getBody();
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', ['path' => $this->request->getPath(), 'request' => $body]);

        if (!$this->validateData($body, $this->productRules())) {
            return $this->validationError($this->validator->getErrors());
        }

        if (!$this->productCategoryModel->getCategoryById((string) $body['category_id'])) {
            return $this->badRequest('Category not found!');
        }

        if ($this->productModel->where('sku', $body['sku'])->first()) {
            return $this->badRequest('SKU already used!');
        }

        if ($this->productModel->getProductBySlug($body['slug'])) {
            return $this->badRequest('Slug already used!');
        }

        try {
            $created = $this->productModel->insertProduct($this->mapProductPayload($body));
            return $this->created('Product created successfully', $created);
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
        $this->RouterCode = 'PRODUCT-EDIT';
        $body = $this->getBody();
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', ['path' => $this->request->getPath(), 'request' => $body]);

        $current = $this->productModel->getProductById((string) $id);
        if (!$current) {
            return $this->notFound('Product not found!');
        }

        if (!$this->validateData($body, $this->productRules())) {
            return $this->validationError($this->validator->getErrors());
        }

        if (!$this->productCategoryModel->getCategoryById((string) $body['category_id'])) {
            return $this->badRequest('Category not found!');
        }

        $skuDuplicate = $this->productModel->where('sku', $body['sku'])->first();
        if ($skuDuplicate && (string) $skuDuplicate['id'] !== (string) $id) {
            return $this->badRequest('SKU already used!');
        }

        $slugDuplicate = $this->productModel->getProductBySlug($body['slug']);
        if ($slugDuplicate && (string) $slugDuplicate['id'] !== (string) $id) {
            return $this->badRequest('Slug already used!');
        }

        try {
            $updated = $this->productModel->updateProductById((string) $id, $this->mapProductPayload($body));
            return $this->success('Product updated successfully', $updated);
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
        $this->RouterCode = 'PRODUCT-DESTROY';
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', ['path' => $this->request->getPath()]);

        if ($this->orderItemModel->where('product_id', $id)->countAllResults() > 0) {
            return $this->badRequest('Product is already used in order items!');
        }

        try {
            $deleted = $this->productModel->destroyProductById((string) $id);
            if (!$deleted) {
                return $this->notFound('Product not found!');
            }

            return $this->success('Product deleted successfully', $deleted);
        } catch (\Throwable $th) {
            return $this->serverError("There's a problem with the server, Contact us!", [
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
            ]);
        }
    }

    private function productRules(): array
    {
        return [
            'category_id' => 'required',
            'sku' => 'required|min_length[2]|max_length[60]',
            'name' => 'required|min_length[2]|max_length[160]',
            'slug' => 'required|min_length[2]|max_length[190]',
            'summary' => 'permit_empty',
            'description' => 'permit_empty',
            'highlight' => 'permit_empty|max_length[255]',
            'price' => 'required|decimal',
            'stock' => 'required|integer',
            'stock_badge' => 'required|in_list[Ready Stock,Pre Order,Limited]',
            'is_featured' => 'required|in_list[0,1]',
            'display' => 'required|in_list[0,1]',
            'sort' => 'required|integer',
        ];
    }

    private function mapProductPayload(array $body): array
    {
        return [
            'category_id' => $body['category_id'],
            'sku' => $body['sku'],
            'name' => $body['name'],
            'slug' => $body['slug'],
            'summary' => $body['summary'] ?? null,
            'description' => $body['description'] ?? null,
            'highlight' => $body['highlight'] ?? null,
            'price' => (float) $body['price'],
            'stock' => (int) $body['stock'],
            'stock_badge' => $body['stock_badge'],
            'is_featured' => filter_var($body['is_featured'], FILTER_VALIDATE_BOOLEAN),
            'display' => filter_var($body['display'], FILTER_VALIDATE_BOOLEAN),
            'sort' => (int) $body['sort'],
        ];
    }
}
