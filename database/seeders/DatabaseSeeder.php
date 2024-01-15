<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Modules\Branches\Database\Seeders\BranchesDatabaseSeeder;
use Modules\Departments\Database\Seeders\DepartmentsDatabaseSeeder;
use Modules\Designations\Database\Seeders\DesignationsDatabaseSeeder;
use Modules\Documents\Database\Seeders\DocumentsDatabaseSeeder;
use Modules\Employees\Database\Seeders\EmployeesDatabaseSeeder;
use Modules\Overtimes\Database\Seeders\OvertimesDatabaseSeeder;
use Modules\Payroll\Database\Seeders\PayrollDatabaseSeeder;
use Modules\Roles\Database\Seeders\RolesDatabaseSeeder;
use Modules\Roles\Models\Role;
use Modules\Users\Database\Seeders\UsersDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            // CountriesSeeder::class,
            // VietnamZoneSeeder::class,
            RolesDatabaseSeeder::class,
            UsersDatabaseSeeder::class,
            BranchesDatabaseSeeder::class,
            DepartmentsDatabaseSeeder::class,
            DesignationsDatabaseSeeder::class,
            EmployeesDatabaseSeeder::class,
            PayrollDatabaseSeeder::class,
            DocumentsDatabaseSeeder::class,
            OvertimesDatabaseSeeder::class,
        ]);

        $email = 'admin@yopmail.com';

        if (!User::query()->where('email', $email)->exists()) {
            User::factory()
                ->create([
                    'name' => 'Admin',
                    'email' => $email,
                    'email_verified_at' => now(),
                    'password' => bcrypt('123123'),
                ])
                ->each(function ($user) {
                    $user->assignRole(Role::ADMIN);
                    $user->syncPermissions($user->getAllPermissions()->pluck('id')->toArray());
                    // $user->employee()->create([
                    //     'first_name' => 'System',
                    //     'last_name' => 'Admin',
                    //     'gender' => 'male',
                    //     'created_by' => $user->id,
                    // ]);
                    // $user->createOwnedTeam(['name' => "$user->name's team"]);
                });

            // User::factory()
            //     ->withEmployee()
            //     ->count(30)
            //     ->create();
        }

        Artisan::call('passport:install');
    }
}
