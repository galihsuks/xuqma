<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
            ],
            'category_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
            ],
            'sku' => [
                'type' => 'VARCHAR',
                'constraint' => 60,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 160,
            ],
            'slug' => [
                'type' => 'VARCHAR',
                'constraint' => 190,
            ],
            'summary' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'description' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'highlight' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'price' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0,
            ],
            'stock' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'stock_badge' => [
                'type' => 'ENUM',
                'constraint' => ['Ready Stock', 'Pre Order', 'Limited'],
                'default' => 'Ready Stock',
            ],
            'is_featured' => [
                'type' => 'BOOLEAN',
                'default' => false,
            ],
            'display' => [
                'type' => 'BOOLEAN',
                'default' => true,
            ],
            'sort' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('category_id');
        $this->forge->addKey('sku');
        $this->forge->addKey('slug');
        $this->forge->addForeignKey('category_id', 'product_categories', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('products');
    }

    public function down()
    {
        $this->forge->dropTable('products');
    }
}
