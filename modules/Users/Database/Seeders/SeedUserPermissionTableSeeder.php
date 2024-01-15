<?php

namespace Modules\Users\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\Roles\Models\Permission;
use Modules\Roles\Models\Role;

class SeedUserPermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $module = 'users::common.module';

        Permission::firstOrCreate([
            'name' => 'users.view',
            'description' => 'permission.view',
            'module' => $module,
            'guard_name' => config('auth.defaults.guard'),
        ]);

        Permission::firstOrCreate([
            'name' => 'users.create',
            'description' => 'permission.create',
            'module' => $module,
            'guard_name' => config('auth.defaults.guard'),
        ]);

        Permission::firstOrCreate([
            'name' => 'users.edit',
            'description' => 'permission.edit',
            'module' => $module,
            'guard_name' => config('auth.defaults.guard'),
        ]);

        Permission::firstOrCreate([
            'name' => 'users.delete',
            'description' => 'permission.delete',
            'module' => $module,
            'guard_name' => config('auth.defaults.guard'),
        ]);

        Permission::firstOrCreate([
            'name' => 'users.edit_user_roles',
            'description' => 'users::common.user_roles',
            'module' => $module,
            'guard_name' => config('auth.defaults.guard'),
        ]);

        Permission::firstOrCreate([
            'name' => 'users.edit_user_permissions',
            'description' => 'users::common.user_permissions',
            'module' => $module,
            'guard_name' => config('auth.defaults.guard'),
        ]);

        Role::first()->syncPermissions(Permission::all());
    }
}
