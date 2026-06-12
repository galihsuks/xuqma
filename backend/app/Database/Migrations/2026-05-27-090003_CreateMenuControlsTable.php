<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMenuControlsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
            ],
            'menu_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null' => false,
            ],
            'code' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
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
        $this->forge->addUniqueKey(['menu_id', 'code']);
        $this->forge->addForeignKey('menu_id', 'app_menus', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('app_menu_controls');
    }

    public function down()
    {
        $this->forge->dropTable('app_menu_controls');
    }
}
