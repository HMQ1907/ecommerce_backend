<?php

namespace Modules\Employees\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Modules\Employees\Models\EmployeeContract;
use Modules\Employees\Notifications\ContractEmployeeEndDateNotification;

class ContractEmployeeEndDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employees:employee-contract-end-date';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send mail to contract employee when contract end date is more than 1 month, 3 weeks, 2 weeks, 1 week';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //Send mail to contract employee when contract end date is more than 1 month, 3 weeks, 2 weeks, 1 week
        $employeeContracts = EmployeeContract::all();
        $oneMonth = Carbon::now()->addMonth()->format('Y-m-d');
        $threeWeeks = Carbon::now()->addWeeks(3)->format('Y-m-d');
        $twoWeeks = Carbon::now()->addWeeks(2)->format('Y-m-d');
        $oneWeek = Carbon::now()->addWeek()->format('Y-m-d');
        if (!empty($employeeContracts)) {
            foreach ($employeeContracts as $employeeContract) {
                if ($employeeContract->contract_to == $oneMonth) {
                    $time = Carbon::parse($employeeContract->contract_to)->diff(Carbon::now()->subDay())->format('%m months, %d days');
                    $employeeContract->employee->notify(new ContractEmployeeEndDateNotification($time));
                }
                if ($employeeContract->contract_to == $threeWeeks) {
                    $time = Carbon::parse($employeeContract->contract_to)->diff(Carbon::now()->subDay())->format('%m months, %d days');
                    $employeeContract->employee->notify(new ContractEmployeeEndDateNotification($time));
                }
                if ($employeeContract->contract_to == $twoWeeks) {
                    $time = Carbon::parse($employeeContract->contract_to)->diff(Carbon::now()->subDay())->format('%m months, %d days');
                    $employeeContract->employee->notify(new ContractEmployeeEndDateNotification($time));
                }
                if ($employeeContract->contract_to == $oneWeek) {
                    $time = Carbon::parse($employeeContract->contract_to)->diff(Carbon::now()->subDay())->format('%m months, %d days');
                    $employeeContract->employee->notify(new ContractEmployeeEndDateNotification($time));
                }
            }
        }

        return Command::SUCCESS;
    }
}
