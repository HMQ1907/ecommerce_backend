<?php

namespace Modules\Roles\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\Roles\Models\Permission;
use Modules\Roles\Models\Role;

class SeedRoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        Role::updateOrCreate([
            'name' => Role::ADMIN,
            'guard_name' => 'api',
        ], [
            'display_name' => 'Admin',
            'description' => 'Admin.',
        ])->syncPermissions(Permission::all());

        Role::updateOrCreate([
            'name' => Role::CUSTOMER,
            'guard_name' => 'api',
        ], [
            'display_name' => 'Customer',
            'description' => 'Customer.',
        ]);
    }
}
