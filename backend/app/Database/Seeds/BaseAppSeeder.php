<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BaseAppSeeder extends Seeder
{
    public function run()
    {
        $this->call('RoleSeeder');
        $this->call('BaseAppMenuSeeder');
        $this->call('EcommerceMenuSeeder');
        $this->call('EcommerceCatalogSeeder');
        $this->call('EcommerceDemoOrderSeeder');
        $this->call('SuperAdminSeeder');
    }
}
