<?php

namespace Modules\Employees\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class EmployeesDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(SeedEmployeePermissionTableSeeder::class);
        $this->call(SeedEmployeeTerminationPermissionTableSeeder::class);
        $this->call(SeedEmployeeTransferPermissionTableSeeder::class);
        $this->call(SeedPublicPermissionTableSeeder::class);
        $this->call(SeedEmployeeAwardPermissionTableSeeder::class);
        $this->call(SeedEmployeeRetaliationPermissionTableSeeder::class);
    }
}
