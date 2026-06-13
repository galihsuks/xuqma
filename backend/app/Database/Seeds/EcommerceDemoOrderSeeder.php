<?php

namespace App\Database\Seeds;

use App\Models\OrderItemModel;
use App\Models\OrderModel;
use App\Models\ProductModel;
use CodeIgniter\Database\Seeder;

class EcommerceDemoOrderSeeder extends Seeder
{
    public function run()
    {
        $orderModel = new OrderModel();
        $orderItemModel = new OrderItemModel();
        $productModel = new ProductModel();

        $products = [
            $productModel->getProductBySlug('voltlink-140w-gan-charger'),
            $productModel->getProductBySlug('nebula-rtx-graphics-card'),
            $productModel->getProductBySlug('titanview-27-2k-monitor'),
        ];

        if (in_array(null, $products, true)) {
            return;
        }

        $orders = [
            [
                'order_number' => 'SO-260613-10001',
                'customer_name' => 'Rafly Pratama',
                'customer_email' => 'rafly@example.test',
                'customer_phone' => '081234567890',
                'channel' => 'Website',
                'status' => 'Waiting Payment',
                'payment_status' => 'Unpaid',
                'notes' => 'Waiting for manual transfer confirmation.',
                'items' => [
                    ['product' => $products[0], 'qty' => 1],
                    ['product' => $products[2], 'qty' => 1],
                ],
            ],
            [
                'order_number' => 'SO-260613-10002',
                'customer_name' => 'Nabila Ayu',
                'customer_email' => 'nabila@example.test',
                'customer_phone' => '081298765432',
                'channel' => 'Website',
                'status' => 'Processing',
                'payment_status' => 'Paid',
                'notes' => 'Payment confirmed, currently preparing packaging.',
                'items' => [
                    ['product' => $products[2], 'qty' => 1],
                ],
            ],
            [
                'order_number' => 'SO-260613-10003',
                'customer_name' => 'Rendy Kurniawan',
                'customer_email' => 'rendy@example.test',
                'customer_phone' => '081211223344',
                'channel' => 'Marketplace Sync',
                'status' => 'Shipped',
                'payment_status' => 'Paid',
                'notes' => 'Courier pickup completed.',
                'items' => [
                    ['product' => $products[1], 'qty' => 1],
                ],
            ],
        ];

        foreach ($orders as $order) {
            $existing = $orderModel->getOrderByNumber($order['order_number']);

            $totalItems = 0;
            $totalAmount = 0.0;
            foreach ($order['items'] as $item) {
                $totalItems += (int) $item['qty'];
                $totalAmount += ((float) $item['product']['price']) * (int) $item['qty'];
            }

            $payload = [
                'user_id' => null,
                'order_number' => $order['order_number'],
                'customer_name' => $order['customer_name'],
                'customer_email' => $order['customer_email'],
                'customer_phone' => $order['customer_phone'],
                'channel' => $order['channel'],
                'status' => $order['status'],
                'payment_status' => $order['payment_status'],
                'total_items' => $totalItems,
                'total_amount' => $totalAmount,
                'notes' => $order['notes'],
            ];

            $savedOrder = $existing
                ? $orderModel->updateOrderById($existing['id'], $payload)
                : $orderModel->insertOrder($payload);

            $orderItemModel->destroyByOrderId($savedOrder['id']);
            foreach ($order['items'] as $item) {
                $orderItemModel->insertOrderItem([
                    'order_id' => $savedOrder['id'],
                    'product_id' => $item['product']['id'],
                    'product_name' => $item['product']['name'],
                    'qty' => (int) $item['qty'],
                    'unit_price' => (float) $item['product']['price'],
                    'subtotal' => ((float) $item['product']['price']) * (int) $item['qty'],
                ]);
            }
        }
    }
}
