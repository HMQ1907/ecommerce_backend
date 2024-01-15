<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Modules\Employees\Models\EmployeeTerminationAllowance;

class UpdateTypeEmployee extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:type-employee';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update type employee when termination date is today';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $terminationEmployees = EmployeeTerminationAllowance::all();
        $currentDate = Carbon::now()->format('Y-m-d');

        if (!empty($terminationEmployees)) {
            foreach ($terminationEmployees as $terminationEmployee) {
                $employee = $terminationEmployee->employee->find($terminationEmployee->employee_id);

                if ($terminationEmployee->termination_date == $currentDate) {
                    $employee->update([
                        'type' => 'removal',
                    ]);
                    $employee->save();
                }
            }
        }

        return Command::SUCCESS;
    }
}
