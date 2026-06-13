<?php

namespace App\Models;

class OrderItemModel extends BaseModel
{
    protected $table = 'order_items';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id',
        'order_id',
        'product_id',
        'product_name',
        'qty',
        'unit_price',
        'subtotal',
    ];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function insertOrderItem(array $params)
    {
        $params['id'] = $this->generateIdItem();
        $this->insert($params);
        return $this->getOrderItemById($params['id']);
    }

    public function getOrderItemById(string $id)
    {
        return $this->select('id, order_id, product_id, product_name, qty, unit_price, subtotal, created_at, updated_at')
            ->where('id', $id)
            ->first();
    }

    public function getItemsByOrderId(string $orderId): array
    {
        return $this->select('id, order_id, product_id, product_name, qty, unit_price, subtotal, created_at, updated_at')
            ->where('order_id', $orderId)
            ->orderBy('created_at', 'ASC')
            ->findAll();
    }

    public function destroyByOrderId(string $orderId): void
    {
        $this->where('order_id', $orderId)->delete();
    }
}
