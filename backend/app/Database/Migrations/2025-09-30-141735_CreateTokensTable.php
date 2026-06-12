<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTokensTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'           => [
                'type'       => 'VARCHAR',
                'constraint' => '36',
            ],
            'user_id'        => [
                'type'       => 'VARCHAR',
                'constraint' => '36',
            ],
            'token'        => [
                'type'       => 'TEXT', // bisa panjang (JWT panjang)
            ],
            'expired_time' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'ip'           => [
                'type'       => 'VARCHAR',
                'constraint' => 45, // support IPv4 & IPv6
                'null'       => true,
            ],
            'device'       => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'platform'     => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'created_at'   => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at'   => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true); // primary key
        $this->forge->createTable('app_tokens');
    }

    public function down()
    {
        $this->forge->dropTable('app_tokens');
    }
}
