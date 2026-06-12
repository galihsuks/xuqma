<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAppSupportTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'key' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'value' => [
                'type'       => 'LONGTEXT',
            ],
            'datatype' => [
                'type'       => 'ENUM',
                'constraint' => ['string', 'number', 'json', 'boolean'],
            ],
            'created_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
            'updated_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
        ]);

        $this->forge->addKey('id', true); // Primary key
        $this->forge->addKey('key', false, true); // unique
        $this->forge->createTable('app_support');
    }

    public function down()
    {
        $this->forge->dropTable('app_support');
    }
}
