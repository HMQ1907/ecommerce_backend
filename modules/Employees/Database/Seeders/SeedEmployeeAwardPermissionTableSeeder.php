<?php

namespace Modules\Employees\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\Roles\Models\Permission;
use Modules\Roles\Models\Role;

class SeedEmployeeAwardPermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $module = 'employees::common.employeeAward';

        Permission::firstOrCreate([
            'name' => 'employee_awards.view',
            'description' => 'permission.view',
            'module' => $module,
            'guard_name' => config('auth.defaults.guard'),
        ]);

        Permission::firstOrCreate([
            'name' => 'employee_awards.create',
            'description' => 'permission.create',
            'module' => $module,
            'guard_name' => config('auth.defaults.guard'),
        ]);

        Permission::firstOrCreate([
            'name' => 'employee_awards.edit',
            'description' => 'permission.edit',
            'module' => $module,
            'guard_name' => config('auth.defaults.guard'),
        ]);

        Permission::firstOrCreate([
            'name' => 'employee_awards.delete',
            'description' => 'permission.delete',
            'module' => $module,
            'guard_name' => config('auth.defaults.guard'),
        ]);

        Role::first()->syncPermissions(Permission::all());
    }
}
