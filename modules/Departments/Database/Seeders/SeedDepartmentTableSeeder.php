<?php

namespace Modules\Departments\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Branches\Models\Branch;
use Modules\Departments\Models\Department;

class SeedDepartmentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $branches = Branch::all();

        foreach ($branches as $key => $branch) {
            $lv1 = Department::firstOrCreate([
                'name' => 'Controller',
                'branch_id' => $branch->id,
                'status' => 1,
            ]);
            $lv2 = Department::firstOrCreate([
                'parent_id' => $lv1->id,
                'name' => 'Board of Members',
                'is_chart' => 0,
                'branch_id' => $branch->id,
                'status' => 1,
            ]);
            $lv3 = Department::firstOrCreate([
                'parent_id' => $lv2->id,
                'name' => 'General Director',
                'branch_id' => $branch->id,
                'status' => 1,
            ]);

            $lv31 = Department::firstOrCreate([
                'parent_id' => $lv3->id,
                'name' => 'Deputy General Director (DGD2)',
                'branch_id' => $branch->id,
                'status' => 1,
            ]);
            $lv32 = Department::firstOrCreate([
                'parent_id' => $lv3->id,
                'name' => 'Finance and Accounting Department',
                'is_chart' => 0,
                'branch_id' => $branch->id,
                'status' => 1,
            ]);
            $lv33 = Department::firstOrCreate([
                'parent_id' => $lv3->id,
                'name' => 'Deputy Director (DD1)',
                'branch_id' => $branch->id,
                'status' => 1,
            ]);
            $lv34 = Department::firstOrCreate([
                'parent_id' => $lv3->id,
                'name' => 'Deputy Director (DD2)',
                'branch_id' => $branch->id,
                'status' => 1,
            ]);
            $lv35 = Department::firstOrCreate([
                'parent_id' => $lv3->id,
                'name' => 'Senior Manager (SMG)',
                'branch_id' => $branch->id,
                'status' => 1,
            ]);
            $lv41 = Department::firstOrCreate([
                'parent_id' => $lv31->id,
                'name' => 'GD Assistant',
                'branch_id' => $branch->id,
                'status' => 1,
            ]);
            if ($key == 0) {
                $lv42 = Department::firstOrCreate([
                    'parent_id' => $lv32->id,
                    'name' => 'Account payable office',
                    'branch_id' => $branch->id,
                    'status' => 1,
                ]);
            }
            $lv43 = Department::firstOrCreate([
                'parent_id' => $lv33->id,
                'name' => 'Logistics & Distribution',
                'is_chart' => 0,
                'branch_id' => $branch->id,
                'status' => 1,
            ]);
            $lv44 = Department::firstOrCreate([
                'parent_id' => $lv34->id,
                'name' => 'Admin & HR Department',
                'is_chart' => 0,
                'branch_id' => $branch->id,
                'status' => 1,
            ]);
            $lv45 = Department::firstOrCreate([
                'parent_id' => $lv35->id,
                'name' => 'General Trading',
                'is_chart' => 0,
                'branch_id' => $branch->id,
                'status' => 1,
            ]);
            Department::firstOrCreate([
                'parent_id' => $lv41->id,
                'name' => 'GD Secretary',
                'branch_id' => $branch->id,
                'status' => 1,
            ]);
            if ($key == 0) {
                Department::firstOrCreate([
                    'parent_id' => $lv32->id,
                    'name' => 'Cash & bank office',
                    'branch_id' => $branch->id,
                    'status' => 1,
                ]);
                Department::firstOrCreate([
                    'parent_id' => $lv32->id,
                    'name' => 'Accounting Assistant',
                    'branch_id' => $branch->id,
                    'status' => 1,
                ]);
                Department::firstOrCreate([
                    'parent_id' => $lv32->id,
                    'name' => 'Assistant IT support office',
                    'branch_id' => $branch->id,
                    'status' => 1,
                ]);
                Department::firstOrCreate([
                    'parent_id' => $lv32->id,
                    'name' => 'IT Admin office',
                    'branch_id' => $branch->id,
                    'status' => 1,
                ]);
                Department::firstOrCreate([
                    'parent_id' => $lv43->id,
                    'name' => 'Stock controller Assistant',
                    'branch_id' => $branch->id,
                    'status' => 1,
                ]);
                Department::firstOrCreate([
                    'parent_id' => $lv43->id,
                    'name' => 'Supply, Custom tax service officer',
                    'branch_id' => $branch->id,
                    'status' => 1,
                ]);
                Department::firstOrCreate([
                    'parent_id' => $lv43->id,
                    'name' => 'Supply & Services officer',
                    'branch_id' => $branch->id,
                    'status' => 1,
                ]);
                Department::firstOrCreate([
                    'parent_id' => $lv43->id,
                    'name' => 'Importing',
                    'branch_id' => $branch->id,
                    'status' => 1,
                ]);
                Department::firstOrCreate([
                    'parent_id' => $lv45->id,
                    'name' => 'B2B Customers',
                    'branch_id' => $branch->id,
                    'status' => 1,
                ]);
                Department::firstOrCreate([
                    'parent_id' => $lv45->id,
                    'name' => 'Domestic Distributors',
                    'branch_id' => $branch->id,
                    'status' => 1,
                ]);
                Department::firstOrCreate([
                    'parent_id' => $lv45->id,
                    'name' => 'Customer Service',
                    'branch_id' => $branch->id,
                    'status' => 1,
                ]);
            } else {
                Department::firstOrCreate([
                    'parent_id' => $lv32->id,
                    'name' => 'Accounting receivable',
                    'branch_id' => $branch->id,
                    'status' => 1,
                ]);
                Department::firstOrCreate([
                    'parent_id' => $lv43->id,
                    'name' => 'Delivery & stock',
                    'branch_id' => $branch->id,
                    'status' => 1,
                ]);
                Department::firstOrCreate([
                    'parent_id' => $lv43->id,
                    'name' => 'Transportation',
                    'branch_id' => $branch->id,
                    'status' => 1,
                ]);
                Department::firstOrCreate([
                    'parent_id' => $lv45->id,
                    'name' => 'Retail Sales and Operation',
                    'branch_id' => $branch->id,
                    'status' => 1,
                ]);
                Department::firstOrCreate([
                    'parent_id' => $lv45->id,
                    'name' => 'Commercial Sales and Operation',
                    'branch_id' => $branch->id,
                    'status' => 1,
                ]);
                Department::firstOrCreate([
                    'parent_id' => $lv45->id,
                    'name' => 'Maintenance',
                    'branch_id' => $branch->id,
                    'status' => 1,
                ]);

            }
            Department::firstOrCreate([
                'parent_id' => $lv32->id,
                'name' => 'Accounting officer',
                'branch_id' => $branch->id,
                'status' => 1,
            ]);
            Department::firstOrCreate([
                'parent_id' => $lv43->id,
                'name' => 'Depots Operation',
                'branch_id' => $branch->id,
                'status' => 1,
            ]);
            Department::firstOrCreate([
                'parent_id' => $lv44->id,
                'name' => 'General Admin officer',
                'branch_id' => $branch->id,
                'status' => 1,
            ]);
            Department::firstOrCreate([
                'parent_id' => $lv44->id,
                'name' => 'Buyer & Material store assistant',
                'branch_id' => $branch->id,
                'status' => 1,
            ]);
            Department::firstOrCreate([
                'parent_id' => $lv44->id,
                'name' => 'Gardenner',
                'branch_id' => $branch->id,
                'status' => 1,
            ]);
            Department::firstOrCreate([
                'parent_id' => $lv44->id,
                'name' => 'Welder',
                'branch_id' => $branch->id,
                'status' => 1,
            ]);
            Department::firstOrCreate([
                'parent_id' => $lv44->id,
                'name' => 'Electrician',
                'branch_id' => $branch->id,
                'status' => 1,
            ]);
        }
    }
}
