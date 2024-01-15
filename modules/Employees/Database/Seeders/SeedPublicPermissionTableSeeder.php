<?php

namespace Modules\Employees\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\Roles\Models\Permission;
use Modules\Roles\Models\Role;

class SeedPublicPermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $dashboard = 'employees::common.dashboard';

        Permission::firstOrCreate([
            'name' => 'dashboard.view',
            'description' => 'permission.view',
            'module' => $dashboard,
            'guard_name' => config('auth.defaults.guard'),
        ]);

        $report = 'employees::common.report';
        Permission::firstOrCreate([
            'name' => 'report.view',
            'description' => 'permission.view',
            'module' => $report,
            'guard_name' => config('auth.defaults.guard'),
        ]);

        Role::first()->syncPermissions(Permission::all());
    }
}
