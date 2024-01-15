<?php

namespace Modules\Teams\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\Roles\Models\Permission;
use Modules\Roles\Models\Role;

class SeedTeamsPermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $module = 'teams::common.module';

        Permission::firstOrCreate([
            'name' => 'teams.view',
            'description' => 'permission.view',
            'module' => $module,
            'guard_name' => config('auth.defaults.guard'),
        ]);

        Permission::firstOrCreate([
            'name' => 'teams.create',
            'description' => 'permission.create',
            'module' => $module,
            'guard_name' => config('auth.defaults.guard'),
        ]);

        Permission::firstOrCreate([
            'name' => 'teams.edit',
            'description' => 'permission.edit',
            'module' => $module,
            'guard_name' => config('auth.defaults.guard'),
        ]);

        Permission::firstOrCreate([
            'name' => 'teams.delete',
            'description' => 'permission.delete',
            'module' => $module,
            'guard_name' => config('auth.defaults.guard'),
        ]);

        Role::first()->syncPermissions(Permission::all());
    }
}
