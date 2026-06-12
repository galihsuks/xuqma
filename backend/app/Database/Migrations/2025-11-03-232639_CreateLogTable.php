<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLogsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true
            ],
            'level' => [
                'type'       => 'ENUM',
                'constraint' => ['info', 'warning', 'error'],
                'default'    => 'info'
            ],
            'message' => [
                'type' => 'TEXT',
            ],
            'context' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Data tambahan (misal JSON)',
            ],
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => true,
            ],
            'created_at' => [
                'type'           => 'DATETIME',
                'null'           => true,
            ],
            'updated_at' => [
                'type'           => 'DATETIME',
                'null'           => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('app_logs');
    }

    public function down()
    {
        $this->forge->dropTable('app_logs');
    }
}
