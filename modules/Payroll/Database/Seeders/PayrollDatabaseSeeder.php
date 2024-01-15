<?php

namespace Modules\Payroll\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class PayrollDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call([
            SeedEmployeeSalaryPermissionTableSeeder::class,
            SeedPayslipPermissionTableSeeder::class,
            SeedSalaryComponentTableSeeder::class,
        ]);

        //        $payroll = new PayrollCycle();
        //        $payroll->cycle = 'monthly';
        //        $payroll->saveQuietly();
        //
        //        $payroll = new PayrollCycle();
        //        $payroll->cycle = 'weekly';
        //        $payroll->saveQuietly();
        //
        //        $payroll = new PayrollCycle();
        //        $payroll->cycle = 'biweekly';
        //        $payroll->saveQuietly();
        //
        //        $payroll = new PayrollCycle();
        //        $payroll->cycle = 'semimonthly';
        //        $payroll->saveQuietly();
        //
        //        $payrollCycle = PayrollCycle::where('cycle', 'monthly')->first();
        //
        //        $salaries = SalarySlip::get();
        //
        //        foreach ($salaries as $salary) {
        //            $dates = $this->getMonthDates($salary->month, $salary->year);
        //
        //            if ($dates) {
        //                $salary->salary_from = $dates['startDate'];
        //                $salary->salary_to = $dates['endDate'];
        //                $salary->payroll_cycle_id = $payrollCycle->id;
        //                $salary->saveQuietly();
        //            }
        //        }
    }

    public function getMonthDates($month, $year)
    {
        $monthDate = Carbon::createFromFormat('Y-m-d', $year.'-'.$month.'-1');
        $startDate = $monthDate->startOfMonth()->format('Y-m-d');
        $endDate = $monthDate->endOfMonth()->format('Y-m-d');

        return [
            'startDate' => $startDate,
            'endDate' => $endDate,
        ];
    }
}
