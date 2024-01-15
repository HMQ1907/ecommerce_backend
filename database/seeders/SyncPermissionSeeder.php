<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Departments\Database\Seeders\SeedDepartmentPermissionTableSeeder;
use Modules\Designations\Database\Seeders\DesignationsDatabaseSeeder;
use Modules\Designations\Database\Seeders\SeedDesignationPermissionTableSeeder;
use Modules\Documents\Database\Seeders\SeedDocumentPermissionTableSeeder;
use Modules\Employees\Database\Seeders\SeedEmployeeAwardPermissionTableSeeder;
use Modules\Employees\Database\Seeders\SeedEmployeePermissionTableSeeder;
use Modules\Employees\Database\Seeders\SeedEmployeeRetaliationPermissionTableSeeder;
use Modules\Employees\Database\Seeders\SeedEmployeeTerminationPermissionTableSeeder;
use Modules\Employees\Database\Seeders\SeedEmployeeTransferPermissionTableSeeder;
use Modules\Employees\Database\Seeders\SeedPublicPermissionTableSeeder;
use Modules\Overtimes\Database\Seeders\SeedOvertimePermissionTableSeeder;
use Modules\Payroll\Database\Seeders\SeedEmployeeSalaryPermissionTableSeeder;
use Modules\Payroll\Database\Seeders\SeedPayslipPermissionTableSeeder;
use Modules\Roles\Database\Seeders\SeedRolePermissionTableSeeder;
use Modules\Roles\Database\Seeders\SeedRoleTableSeeder;
use Modules\Roles\Models\Permission;
use Modules\Users\Database\Seeders\SeedUserPermissionTableSeeder;

class SyncPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            DB::beginTransaction();

            Permission::query()->delete();

            $this->call([
                SeedRoleTableSeeder::class,
                SeedRolePermissionTableSeeder::class,
                SeedUserPermissionTableSeeder::class,
                SeedEmployeePermissionTableSeeder::class,
                SeedEmployeeSalaryPermissionTableSeeder::class,
                SeedPayslipPermissionTableSeeder::class,
                SeedDepartmentPermissionTableSeeder::class,
                DesignationsDatabaseSeeder::class,
                SeedDocumentPermissionTableSeeder::class,
                SeedDesignationPermissionTableSeeder::class,
                SeedEmployeeAwardPermissionTableSeeder::class,
                SeedPublicPermissionTableSeeder::class,
                SeedEmployeeTerminationPermissionTableSeeder::class,
                SeedOvertimePermissionTableSeeder::class,
                SeedEmployeeTransferPermissionTableSeeder::class,
                SeedEmployeeRetaliationPermissionTableSeeder::class,
            ]);

            foreach (User::all() as $user) {
                $user->assignRole($user->getRoleNames()->first());
                $user->syncPermissions($user->getAllPermissions()->pluck('id')->toArray());
            }

            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }
}
