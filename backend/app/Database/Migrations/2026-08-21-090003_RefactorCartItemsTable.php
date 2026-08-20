<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RefactorCartItemsTable extends Migration
{
    public function up()
    {
        $fields = $this->db->getFieldNames('cart_items');
        $columnsToDrop = array_values(array_intersect(
            ['product_name', 'category_name', 'price', 'subtotal'],
            $fields,
        ));

        if ($columnsToDrop !== []) {
            $this->forge->dropColumn('cart_items', $columnsToDrop);
        }
    }

    public function down()
    {
        $fields = $this->db->getFieldNames('cart_items');
        $columnsToAdd = [];

        if (!in_array('product_name', $fields, true)) {
            $columnsToAdd['product_name'] = [
                'type' => 'VARCHAR',
                'constraint' => 180,
                'null' => true,
                'after' => 'product_id',
            ];
        }

        if (!in_array('category_name', $fields, true)) {
            $columnsToAdd['category_name'] = [
                'type' => 'VARCHAR',
                'constraint' => 120,
                'null' => true,
                'after' => 'product_name',
            ];
        }

        if (!in_array('price', $fields, true)) {
            $columnsToAdd['price'] = [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0,
                'after' => 'category_name',
            ];
        }

        if (!in_array('subtotal', $fields, true)) {
            $columnsToAdd['subtotal'] = [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0,
                'after' => 'qty',
            ];
        }

        if ($columnsToAdd !== []) {
            $this->forge->addColumn('cart_items', $columnsToAdd);
        }
    }
}
