<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRoleMenuControlsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
            ],
            'role_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null' => false,
            ],
            'menu_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null' => false,
            ],
            'menu_control_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null' => false,
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
        $this->forge->addUniqueKey(['role_id', 'menu_id', 'menu_control_id']);
        $this->forge->addForeignKey('role_id', 'app_roles', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('menu_id', 'app_menus', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('menu_control_id', 'app_menu_controls', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('app_role_menu_controls');
    }

    public function down()
    {
        $this->forge->dropTable('app_role_menu_controls');
    }
}
