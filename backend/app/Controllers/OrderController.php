<?php

namespace App\Controllers;

class OrderController extends BaseController
{
    public function index()
    {
        $this->UUID = strtoupper(uniqid());
        $this->UserID = $this->request->user['id'] ?? '';
        $this->RouterCode = 'ORDER-INDEX';
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $defaultPageSize = (int) $this->orderModel->LIMIT_DATA;
        $pageSize = max(1, min(100, (int) ($this->request->getGet('page_size') ?? $defaultPageSize)));
        $keywords = trim((string) ($this->request->getGet('keywords') ?? ''));
        $status = trim((string) ($this->request->getGet('status') ?? ''));
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', [
            'path' => $this->request->getPath(),
            'query' => $this->request->getGet(),
        ]);

        $isPrivileged = $this->isPrivilegedOrderViewer();
        $result = $isPrivileged
            ? $this->orderModel->getOrderListPaginated($page, $pageSize, $keywords, $status)
            : $this->orderModel->getCustomerOrderListPaginated(
                $page,
                $pageSize,
                $keywords,
                $status,
                $this->getLoginUser('id'),
                $this->getLoginUser('email'),
            );
        $items = $result['items'];
        $totalItems = (int) $result['total_items'];
        $totalPages = max(1, (int) ceil($totalItems / $pageSize));

        return $this->success('List order', $items, [
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
        $this->RouterCode = 'ORDER-DETAIL';
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', ['path' => $this->request->getPath()]);

        $order = $this->orderModel->getOrderById((string) $id);
        if (!$order) {
            return $this->notFound('Order not found!');
        }

        if (!$this->isPrivilegedOrderViewer() && !$this->orderModel->belongsToCustomer(
            (string) $id,
            $this->getLoginUser('id'),
            $this->getLoginUser('email'),
        )) {
            return $this->forbidden('You do not have access to this order!');
        }

        $order['items'] = $this->orderItemModel->getItemsByOrderId((string) $id);
        return $this->success('Detail order', $order);
    }

    public function create()
    {
        $this->UUID = strtoupper(uniqid());
        $this->UserID = $this->request->user['id'] ?? '';
        $this->RouterCode = 'ORDER-CREATE';
        $body = $this->getBody();
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', ['path' => $this->request->getPath(), 'request' => $body]);

        if (!$this->validateData($body, $this->orderRules())) {
            return $this->validationError($this->validator->getErrors());
        }

        $items = $body['items'] ?? [];
        if (!is_array($items) || $items === []) {
            return $this->badRequest('Order items are required!');
        }

        $calculated = $this->normalizeOrderItems($items);
        if ($calculated['items'] === []) {
            return $this->badRequest('Valid order items are required!');
        }

        try {
            $orderNumber = $this->generateOrderNumber();
            $created = $this->orderModel->insertOrder([
                'user_id' => $this->resolveOrderUserId($body),
                'order_number' => $orderNumber,
                'customer_name' => $this->resolveOrderCustomerName($body),
                'customer_email' => $this->resolveOrderCustomerEmail($body),
                'customer_phone' => $body['customer_phone'] ?? null,
                'channel' => $body['channel'],
                'status' => $body['status'],
                'payment_status' => $body['payment_status'],
                'total_items' => $calculated['total_items'],
                'total_amount' => $calculated['total_amount'],
                'notes' => $body['notes'] ?? null,
            ]);

            foreach ($calculated['items'] as $item) {
                $this->orderItemModel->insertOrderItem([
                    'order_id' => $created['id'],
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'qty' => $item['qty'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            $detail = $this->orderModel->getOrderById($created['id']);
            $detail['items'] = $this->orderItemModel->getItemsByOrderId($created['id']);

            if (!$this->isPrivilegedOrderViewer()) {
                $this->clearCustomerCartByUserId($this->getLoginUser('id'));
            }

            return $this->created('Order created successfully', $detail);
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
        $this->RouterCode = 'ORDER-EDIT';
        $body = $this->getBody();
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', ['path' => $this->request->getPath(), 'request' => $body]);

        $current = $this->orderModel->getOrderById((string) $id);
        if (!$current) {
            return $this->notFound('Order not found!');
        }

        if (!$this->isPrivilegedOrderViewer()) {
            return $this->forbidden('Customer cannot update order records!');
        }

        if (!$this->validateData($body, $this->orderRules(false))) {
            return $this->validationError($this->validator->getErrors());
        }

        $items = $body['items'] ?? [];
        if (!is_array($items) || $items === []) {
            return $this->badRequest('Order items are required!');
        }

        $calculated = $this->normalizeOrderItems($items);
        if ($calculated['items'] === []) {
            return $this->badRequest('Valid order items are required!');
        }

        try {
            $updated = $this->orderModel->updateOrderById((string) $id, [
                'user_id' => $this->resolveOrderUserId($body),
                'customer_name' => $this->resolveOrderCustomerName($body),
                'customer_email' => $this->resolveOrderCustomerEmail($body),
                'customer_phone' => $body['customer_phone'] ?? null,
                'channel' => $body['channel'],
                'status' => $body['status'],
                'payment_status' => $body['payment_status'],
                'total_items' => $calculated['total_items'],
                'total_amount' => $calculated['total_amount'],
                'notes' => $body['notes'] ?? null,
            ]);

            $this->orderItemModel->destroyByOrderId((string) $id);
            foreach ($calculated['items'] as $item) {
                $this->orderItemModel->insertOrderItem([
                    'order_id' => (string) $id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'qty' => $item['qty'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            $updated['items'] = $this->orderItemModel->getItemsByOrderId((string) $id);
            return $this->success('Order updated successfully', $updated);
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
        $this->RouterCode = 'ORDER-DESTROY';
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', ['path' => $this->request->getPath()]);

        if (!$this->isPrivilegedOrderViewer()) {
            return $this->forbidden('Customer cannot delete order records!');
        }

        try {
            $deleted = $this->orderModel->destroyOrderById((string) $id);
            if (!$deleted) {
                return $this->notFound('Order not found!');
            }

            return $this->success('Order deleted successfully', $deleted);
        } catch (\Throwable $th) {
            return $this->serverError("There's a problem with the server, Contact us!", [
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
            ]);
        }
    }

    private function orderRules(bool $requireOrderNumber = true): array
    {
        return [
            'user_id' => 'permit_empty',
            'customer_name' => 'required|min_length[2]|max_length[150]',
            'customer_email' => 'permit_empty|valid_email|max_length[150]',
            'customer_phone' => 'permit_empty|max_length[50]',
            'channel' => 'required|min_length[2]|max_length[50]',
            'status' => 'required|in_list[Waiting Payment,Processing,Packed,Shipped,Completed,Cancelled]',
            'payment_status' => 'required|in_list[Unpaid,Paid,Refunded]',
            'notes' => 'permit_empty',
        ];
    }

    private function normalizeOrderItems(array $items): array
    {
        $result = [];
        $totalItems = 0;
        $totalAmount = 0.0;

        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }

            $qty = max(1, (int) ($item['qty'] ?? 0));
            $unitPrice = (float) ($item['unit_price'] ?? 0);
            $productId = (string) ($item['product_id'] ?? '');
            $productName = trim((string) ($item['product_name'] ?? ''));

            if ($productId !== '') {
                $product = $this->productModel->getProductById($productId);
                if ($product) {
                    $productName = (string) $product['name'];
                    if ($unitPrice <= 0) {
                        $unitPrice = (float) $product['price'];
                    }
                }
            }

            if ($productName === '' || $unitPrice <= 0) {
                continue;
            }

            $subtotal = $qty * $unitPrice;
            $result[] = [
                'product_id' => $productId !== '' ? $productId : null,
                'product_name' => $productName,
                'qty' => $qty,
                'unit_price' => $unitPrice,
                'subtotal' => $subtotal,
            ];
            $totalItems += $qty;
            $totalAmount += $subtotal;
        }

        return [
            'items' => $result,
            'total_items' => $totalItems,
            'total_amount' => $totalAmount,
        ];
    }

    private function generateOrderNumber(): string
    {
        return 'SO-' . date('ymd') . '-' . substr((string) round(microtime(true) * 1000), -5);
    }

    private function isPrivilegedOrderViewer(): bool
    {
        $role = strtoupper((string) ($this->getLoginUser('role') ?? ''));
        return in_array($role, ['SA', 'A', 'ADMIN', 'SUPERADMIN', 'SUPER_ADMIN'], true);
    }

    private function resolveOrderUserId(array $body): ?string
    {
        if (!$this->isPrivilegedOrderViewer()) {
            return $this->getLoginUser('id');
        }

        $userId = trim((string) ($body['user_id'] ?? ''));
        return $userId !== '' ? $userId : null;
    }

    private function resolveOrderCustomerName(array $body): string
    {
        if (!$this->isPrivilegedOrderViewer()) {
            $loginUser = $this->userModel->find($this->getLoginUser('id'));
            $fullName = trim((string) ($loginUser['full_name'] ?? ''));
            if ($fullName !== '') {
                return $fullName;
            }
        }

        return (string) $body['customer_name'];
    }

    private function resolveOrderCustomerEmail(array $body): ?string
    {
        if (!$this->isPrivilegedOrderViewer()) {
            $email = trim((string) ($this->getLoginUser('email') ?? ''));
            return $email !== '' ? $email : null;
        }

        $email = trim((string) ($body['customer_email'] ?? ''));
        return $email !== '' ? $email : null;
    }
}
