<?php

namespace Modules\Departments\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class DepartmentsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(SeedDepartmentPermissionTableSeeder::class);
        $this->call(SeedDepartmentTableSeeder::class);
    }
}
