<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RenameEcommerceTables extends Migration
{
    public function up()
    {
        $this->renameIfExists('app_product_categories', 'product_categories');
        $this->renameIfExists('app_products', 'products');
        $this->renameIfExists('app_articles', 'articles');
        $this->renameIfExists('app_orders', 'orders');
        $this->renameIfExists('app_order_items', 'order_items');
    }

    public function down()
    {
        $this->renameIfExists('order_items', 'app_order_items');
        $this->renameIfExists('orders', 'app_orders');
        $this->renameIfExists('articles', 'app_articles');
        $this->renameIfExists('products', 'app_products');
        $this->renameIfExists('product_categories', 'app_product_categories');
    }

    private function renameIfExists(string $from, string $to): void
    {
        if (!$this->db->tableExists($from) || $this->db->tableExists($to)) {
            return;
        }

        $this->forge->renameTable($from, $to);
    }
}
