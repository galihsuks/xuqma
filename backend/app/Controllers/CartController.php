<?php

namespace App\Controllers;

class CartController extends BaseController
{
    public function index()
    {
        $this->UUID = strtoupper(uniqid());
        $this->UserID = (string) ($this->getLoginUser('id') ?? '');
        $this->RouterCode = 'CART-INDEX';
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', ['path' => $this->request->getPath()]);

        if ($forbidden = $this->ensureCustomerRole()) {
            return $forbidden;
        }

        return $this->success('Current cart loaded.', $this->getCurrentCartDetail());
    }

    public function count()
    {
        $this->UUID = strtoupper(uniqid());
        $this->UserID = (string) ($this->getLoginUser('id') ?? '');
        $this->RouterCode = 'CART-COUNT';
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', ['path' => $this->request->getPath()]);

        if ($forbidden = $this->ensureCustomerRole()) {
            return $forbidden;
        }

        $detail = $this->getCurrentCartDetail();

        return $this->success('Cart count loaded.', [
            'total_qty' => $detail['total_qty'],
            'total_lines' => count($detail['items']),
        ]);
    }

    public function addItem()
    {
        $this->UUID = strtoupper(uniqid());
        $this->UserID = (string) ($this->getLoginUser('id') ?? '');
        $this->RouterCode = 'CART-ADD';
        $body = $this->getBody();
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', ['path' => $this->request->getPath(), 'request' => $body]);

        if ($forbidden = $this->ensureCustomerRole()) {
            return $forbidden;
        }

        $productId = trim((string) ($body['product_id'] ?? ''));
        $qty = max(1, (int) ($body['qty'] ?? 1));
        if ($productId === '') {
            return $this->badRequest('Product is required!');
        }

        $product = $this->resolveCartProduct($productId);
        if ($product === null) {
            return $this->notFound('Product not found or unavailable!');
        }

        $cart = $this->cartModel->getOrCreateCartByUserId($this->UserID);
        $existing = $this->cartItemModel->getItemByCartAndProductId($cart['id'], $productId);

        if ($existing) {
            $newQty = (int) $existing['qty'] + $qty;
            $this->cartItemModel->update((string) $existing['id'], [
                'qty' => $newQty,
            ]);
        } else {
            $this->cartItemModel->insertCartItem([
                'cart_id' => $cart['id'],
                'product_id' => $productId,
                'qty' => $qty,
            ]);
        }

        $detail = $this->refreshCartTotals($cart['id']);

        return $this->success($product['name'] . ' added to cart.', $detail);
    }

    public function updateItem($productId)
    {
        $this->UUID = strtoupper(uniqid());
        $this->UserID = (string) ($this->getLoginUser('id') ?? '');
        $this->RouterCode = 'CART-UPDATE';
        $body = $this->getBody();
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', ['path' => $this->request->getPath(), 'request' => $body]);

        if ($forbidden = $this->ensureCustomerRole()) {
            return $forbidden;
        }

        $cart = $this->cartModel->getOrCreateCartByUserId($this->UserID);
        $item = $this->cartItemModel->getItemByCartAndProductId($cart['id'], (string) $productId);
        if ($item === null) {
            return $this->notFound('Cart item not found!');
        }

        $qty = max(1, (int) ($body['qty'] ?? 1));

        $this->cartItemModel->update((string) $item['id'], [
            'qty' => $qty,
        ]);

        $detail = $this->refreshCartTotals($cart['id']);

        return $this->success('Cart item updated.', $detail);
    }

    public function destroyItem($productId)
    {
        $this->UUID = strtoupper(uniqid());
        $this->UserID = (string) ($this->getLoginUser('id') ?? '');
        $this->RouterCode = 'CART-DESTROY';
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', ['path' => $this->request->getPath()]);

        if ($forbidden = $this->ensureCustomerRole()) {
            return $forbidden;
        }

        $cart = $this->cartModel->getOrCreateCartByUserId($this->UserID);
        $item = $this->cartItemModel->getItemByCartAndProductId($cart['id'], (string) $productId);
        if ($item === null) {
            return $this->notFound('Cart item not found!');
        }

        $this->cartItemModel->delete((string) $item['id']);
        $detail = $this->refreshCartTotals($cart['id']);

        return $this->success('Cart item removed.', $detail);
    }

    public function clear()
    {
        $this->UUID = strtoupper(uniqid());
        $this->UserID = (string) ($this->getLoginUser('id') ?? '');
        $this->RouterCode = 'CART-CLEAR';
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', ['path' => $this->request->getPath()]);

        if ($forbidden = $this->ensureCustomerRole()) {
            return $forbidden;
        }

        $this->clearCustomerCartByUserId($this->UserID);

        return $this->success('Cart cleared.', $this->getCurrentCartDetail());
    }

    private function ensureCustomerRole()
    {
        $role = strtoupper((string) ($this->getLoginUser('role') ?? ''));
        if ($role !== 'C') {
            return $this->forbidden('Only customer accounts can access cart data.');
        }

        return null;
    }

    private function resolveCartProduct(string $productId): ?array
    {
        $product = $this->productModel->getProductById($productId);
        if (!$product) {
            return null;
        }

        if ((int) ($product['display'] ?? 0) !== 1) {
            return null;
        }

        return $product;
    }

    private function getCurrentCartDetail(): array
    {
        $cart = $this->cartModel->getCartByUserId($this->UserID);
        if ($cart === null) {
            return [
                'id' => null,
                'user_id' => $this->UserID,
                'total_qty' => 0,
                'total_lines' => 0,
                'subtotal_amount' => 0,
                'items' => [],
            ];
        }

        $items = $this->cartItemModel->getItemsByCartId((string) $cart['id']);
        $subtotalAmount = array_reduce($items, static function ($carry, array $item) {
            return $carry + (float) ($item['subtotal'] ?? 0);
        }, 0.0);

        return [
            'id' => $cart['id'],
            'user_id' => $cart['user_id'],
            'total_qty' => (int) ($cart['total_qty'] ?? 0),
            'total_lines' => count($items),
            'subtotal_amount' => $subtotalAmount,
            'items' => $items,
        ];
    }

    private function refreshCartTotals(string $cartId): array
    {
        $items = $this->cartItemModel->getItemsByCartId($cartId);
        $totalQty = array_reduce($items, static function ($carry, array $item) {
            return $carry + (int) ($item['qty'] ?? 0);
        }, 0);

        $this->cartModel->updateTotalQty($cartId, $totalQty);

        return $this->getCurrentCartDetail();
    }
}
