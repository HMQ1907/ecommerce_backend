<?php

namespace Modules\Payroll\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\Payroll\Models\SalaryComponent;

class SeedSalaryComponentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $salaryComponents = [
            [
                'name' => 'Housing allowance',
                'type' => SalaryComponent::EARNING_TYPE,
                'value' => 0,
                'value_type' => 'variable',
                'is_company' => false,
                'weight_formulate' => null,
            ],
            [
                'name' => 'New position allowance',
                'type' => SalaryComponent::EARNING_TYPE,
                'value' => 0,
                'value_type' => 'variable',
                'is_company' => false,
                'weight_formulate' => null,
            ],
            // [
            //     'name' => 'Termination allowance',
            //     'type' => SalaryComponent::EARNING_TYPE,
            //     'value' => 0,
            //     'value_type' => 'variable',
            //     'is_company' => false,
            //     'weight_formulate' => null,
            // ],
            [
                'name' => 'Remuneration',
                'type' => SalaryComponent::EARNING_TYPE,
                'value' => 0,
                'value_type' => 'variable',
                'is_company' => false,
                'weight_formulate' => null,
            ],
        ];

        foreach ($salaryComponents as $salaryComponent) {
            SalaryComponent::firstOrCreate([
                'name' => $salaryComponent['name'],
                'type' => $salaryComponent['type'],
                'value' => $salaryComponent['value'],
                'value_type' => $salaryComponent['value_type'],
                'is_company' => $salaryComponent['is_company'],
                'weight_formulate' => $salaryComponent['weight_formulate'],
            ]);
        }
    }
}
