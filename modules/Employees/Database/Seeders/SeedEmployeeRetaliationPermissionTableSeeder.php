<?php

namespace Modules\Employees\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\Roles\Models\Permission;
use Modules\Roles\Models\Role;

class SeedEmployeeRetaliationPermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $module = 'employees::common.employeeRetaliation';

        Permission::firstOrCreate([
            'name' => 'employee_retaliations.view',
            'description' => 'permission.view',
            'module' => $module,
            'guard_name' => config('auth.defaults.guard'),
        ]);

        Permission::firstOrCreate([
            'name' => 'employee_retaliations.create',
            'description' => 'permission.create',
            'module' => $module,
            'guard_name' => config('auth.defaults.guard'),
        ]);

        Permission::firstOrCreate([
            'name' => 'employee_retaliations.edit',
            'description' => 'permission.edit',
            'module' => $module,
            'guard_name' => config('auth.defaults.guard'),
        ]);

        Permission::firstOrCreate([
            'name' => 'employee_retaliations.delete',
            'description' => 'permission.delete',
            'module' => $module,
            'guard_name' => config('auth.defaults.guard'),
        ]);

        Role::first()->syncPermissions(Permission::all());
    }
}
