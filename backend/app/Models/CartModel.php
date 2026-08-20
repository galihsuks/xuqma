<?php

namespace App\Models;

class CartModel extends BaseModel
{
    protected $table = 'carts';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id',
        'user_id',
        'total_qty',
    ];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getCartByUserId(string $userId): ?array
    {
        $cart = $this->where('user_id', $userId)->first();

        return $cart ?: null;
    }

    public function getOrCreateCartByUserId(string $userId): array
    {
        $cart = $this->getCartByUserId($userId);
        if ($cart) {
            return $cart;
        }

        $id = $this->generateIdItem();
        $this->insert([
            'id' => $id,
            'user_id' => $userId,
            'total_qty' => 0,
        ]);

        return (array) $this->find($id);
    }

    public function updateTotalQty(string $cartId, int $totalQty): void
    {
        $this->update($cartId, [
            'total_qty' => max(0, $totalQty),
        ]);
    }
}
