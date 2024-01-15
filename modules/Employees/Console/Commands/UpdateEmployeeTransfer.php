<?php

namespace Modules\Employees\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Modules\Employees\Models\EmployeeTransfer;
use Modules\Employees\Repositories\EmployeeRepository;
use Modules\Payroll\Repositories\EmployeeSalaryRepository;
use Modules\Payroll\Services\EmployeeSalaryService;

class UpdateEmployeeTransfer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employees:employee-transfer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update employee transfer when transfer date is today';

    protected $employeeSalaryRepository;

    protected $employeeRepository;

    public function __construct(EmployeeSalaryRepository $employeeSalaryRepository, EmployeeRepository $employeeRepository)
    {
        parent::__construct();
        $this->employeeSalaryRepository = $employeeSalaryRepository;
        $this->employeeRepository = $employeeRepository;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $employeesTransfers = EmployeeTransfer::all();
        $currentDate = Carbon::now()->format('Y-m-d');

        if (!empty($employeesTransfers)) {
            foreach ($employeesTransfers as $employeesTransfer) {
                if ($employeesTransfer->transfer_date == $currentDate) {
                    $employeesTransfer->employee->update([
                        'branch_id' => $employeesTransfer->to_branch_id,
                        'designation_id' => $employeesTransfer->to_designation_id,
                        'department_id' => $employeesTransfer->to_department_id,
                        'job' => $employeesTransfer->job,
                    ]);
                    $employeesTransfer->save();

                    $employeeSalaryInfo['basic_salary'] = $employeesTransfer->new_salary;
                    $employeeSalaryInfo['variable_salaries'] = [
                        [
                            'variable_component_id' => 2, // New Position Allowance
                            'variable_value' => $employeesTransfer->new_position_allowance,
                        ],
                    ];
                    (new EmployeeSalaryService($this->employeeSalaryRepository, $this->employeeRepository))->updateEmployeeSalary($employeeSalaryInfo, $employeesTransfer->employee_id);
                }
            }
        }

        return Command::SUCCESS;
    }
}
