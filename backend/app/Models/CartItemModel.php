<?php

namespace App\Models;

class CartItemModel extends BaseModel
{
    protected $table = 'cart_items';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id',
        'cart_id',
        'product_id',
        'qty',
    ];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getItemsByCartId(string $cartId): array
    {
        return $this->baseSelect()
            ->where('cart_items.cart_id', $cartId)
            ->orderBy('cart_items.created_at', 'ASC')
            ->findAll();
    }

    public function getItemByCartAndProductId(string $cartId, string $productId): ?array
    {
        $item = $this->baseSelect()
            ->where('cart_items.cart_id', $cartId)
            ->where('cart_items.product_id', $productId)
            ->first();

        return $item ?: null;
    }

    public function insertCartItem(array $params): array
    {
        $params['id'] = $this->generateIdItem();
        $this->insert($params);

        return (array) $this->getCartItemById($params['id']);
    }

    public function getCartItemById(string $id): ?array
    {
        $item = $this->baseSelect()
            ->where('cart_items.id', $id)
            ->first();

        return $item ?: null;
    }

    public function destroyByCartId(string $cartId): void
    {
        $this->where('cart_id', $cartId)->delete();
    }

    private function baseSelect()
    {
        return $this->select('cart_items.id, cart_items.cart_id, cart_items.product_id, products.name as product_name, product_categories.name as category_name, products.price as price, cart_items.qty, (cart_items.qty * products.price) as subtotal, cart_items.created_at, cart_items.updated_at')
            ->join('products', 'products.id = cart_items.product_id', 'inner')
            ->join('product_categories', 'product_categories.id = products.category_id', 'left');
    }
}
