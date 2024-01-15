<?php

namespace Modules\Roles\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class RolesDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(SeedRoleTableSeeder::class);
        $this->call(SeedRolePermissionTableSeeder::class);
    }
}
