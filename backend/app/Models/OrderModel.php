<?php

namespace App\Models;

class OrderModel extends BaseModel
{
    protected $table = 'orders';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id',
        'user_id',
        'order_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'channel',
        'status',
        'payment_status',
        'total_items',
        'total_amount',
        'notes',
    ];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function insertOrder(array $params)
    {
        $params['id'] = $this->generateIdItem();
        $this->insert($params);
        return $this->getOrderById($params['id']);
    }

    public function getOrderById(string $id)
    {
        return $this->baseSelect()
            ->where('orders.id', $id)
            ->first();
    }

    public function getOrderByNumber(string $orderNumber)
    {
        return $this->baseSelect()
            ->where('orders.order_number', $orderNumber)
            ->first();
    }

    public function updateOrderById(string $id, array $params)
    {
        $updated = $this->update($id, $params);
        if ($updated) {
            return $this->getOrderById($id);
        }
        return null;
    }

    public function destroyOrderById(string $id)
    {
        $data = $this->getOrderById($id);
        if ($data) {
            $this->delete($id);
            return $data;
        }
        return null;
    }

    public function getOrderListPaginated(int $page, int $pageSize, string $keywords = '', string $status = ''): array
    {
        $offset = max(0, ($page - 1) * $pageSize);
        $builder = $this->buildOrderListBuilder($keywords, $status);

        $totalItems = (int) $builder->countAllResults(false);
        $items = $builder
            ->orderBy('orders.created_at', 'DESC')
            ->get($pageSize, $offset)
            ->getResultArray();

        return [
            'items' => $items,
            'total_items' => $totalItems,
        ];
    }

    public function getCustomerOrderListPaginated(
        int $page,
        int $pageSize,
        string $keywords = '',
        string $status = '',
        ?string $userId = null,
        ?string $email = null,
    ): array {
        $offset = max(0, ($page - 1) * $pageSize);
        $builder = $this->buildOrderListBuilder($keywords, $status);
        $this->applyCustomerScope($builder, $userId, $email);

        $totalItems = (int) $builder->countAllResults(false);
        $items = $builder
            ->orderBy('orders.created_at', 'DESC')
            ->get($pageSize, $offset)
            ->getResultArray();

        return [
            'items' => $items,
            'total_items' => $totalItems,
        ];
    }

    public function belongsToCustomer(string $orderId, ?string $userId = null, ?string $email = null): bool
    {
        $builder = $this->db->table($this->table)
            ->select('orders.id')
            ->where('orders.id', $orderId);

        $this->applyCustomerScope($builder, $userId, $email);

        return (bool) $builder->get()->getRowArray();
    }

    private function buildOrderListBuilder(string $keywords = '', string $status = '')
    {
        $builder = $this->db->table($this->table)
            ->select('orders.id, orders.user_id, app_users.full_name as user_full_name, orders.order_number, orders.customer_name, orders.customer_email, orders.customer_phone, orders.channel, orders.status, orders.payment_status, orders.total_items, orders.total_amount, orders.notes, orders.created_at, orders.updated_at')
            ->join('app_users', 'app_users.id = orders.user_id', 'left');

        if ($keywords !== '') {
            $builder->groupStart()
                ->like('orders.order_number', $keywords)
                ->orLike('orders.customer_name', $keywords)
                ->orLike('orders.customer_email', $keywords)
                ->orLike('orders.channel', $keywords)
                ->groupEnd();
        }

        if ($status !== '') {
            $builder->where('orders.status', $status);
        }

        return $builder;
    }

    private function applyCustomerScope($builder, ?string $userId = null, ?string $email = null): void
    {
        $builder->groupStart();

        $hasScope = false;
        if ($userId !== null && $userId !== '') {
            $builder->where('orders.user_id', $userId);
            $hasScope = true;
        }

        if ($email !== null && $email !== '') {
            if ($hasScope) {
                $builder->orWhere('orders.customer_email', $email);
            } else {
                $builder->where('orders.customer_email', $email);
                $hasScope = true;
            }
        }

        if (!$hasScope) {
            $builder->where('1 = 0');
        }

        $builder->groupEnd();
    }

    private function baseSelect()
    {
        return $this->select('orders.id, orders.user_id, app_users.full_name as user_full_name, orders.order_number, orders.customer_name, orders.customer_email, orders.customer_phone, orders.channel, orders.status, orders.payment_status, orders.total_items, orders.total_amount, orders.notes, orders.created_at, orders.updated_at')
            ->join('app_users', 'app_users.id = orders.user_id', 'left');
    }
}
