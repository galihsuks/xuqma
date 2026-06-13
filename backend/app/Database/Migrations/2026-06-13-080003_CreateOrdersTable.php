<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
            ],
            'user_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null' => true,
            ],
            'order_number' => [
                'type' => 'VARCHAR',
                'constraint' => 60,
            ],
            'customer_name' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
            ],
            'customer_email' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
            ],
            'customer_phone' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'channel' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'Website',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['Waiting Payment', 'Processing', 'Packed', 'Shipped', 'Completed', 'Cancelled'],
                'default' => 'Waiting Payment',
            ],
            'payment_status' => [
                'type' => 'ENUM',
                'constraint' => ['Unpaid', 'Paid', 'Refunded'],
                'default' => 'Unpaid',
            ],
            'total_items' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'total_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0,
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
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
        $this->forge->addKey('user_id');
        $this->forge->addKey('order_number');
        $this->forge->addForeignKey('user_id', 'app_users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('orders');
    }

    public function down()
    {
        $this->forge->dropTable('orders');
    }
}
