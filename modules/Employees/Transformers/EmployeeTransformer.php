<?php

namespace Modules\Employees\Transformers;

use App\Transformers\BaseTransformer;
use Modules\Departments\Transformers\EmployeeDepartmentTransformer;
use Modules\Designations\Transformers\EmployeeDesignationTransformer;
use Modules\Employees\Models\Employee;
use Modules\Overtimes\Transformers\OvertimeTransformer;
use Modules\Users\Transformers\UserTransformer;

class EmployeeTransformer extends BaseTransformer
{
    /**
     * Include resources without needing it to be requested.
     */
    protected array $defaultIncludes = [
        'user',
        'department',
        'designation',
        'bank_accounts',
        'contracts',
        'terminationAllowances',
        'employeeAwards',
        'overtimes',
        'retaliations',
    ];

    /**
     * Transform the Employee entity.
     *
     * @return array
     */
    public function transform(Employee $model)
    {
        return [
            'id' => (int) $model->id,
            'user_id' => $model->user_id,
            'employee_code' => $model->employee_code,
            'first_name' => $model->first_name,
            'last_name' => $model->last_name,
            'full_name' => $model->full_name,
            'currency_code' => $model->currency_code,
            'gender' => $model->gender,
            'email' => $model->email,
            'department_id' => $model->department_id,
            'date_of_birth' => $model->date_of_birth,
            'avatar_url' => $model->avatar_url,
            'phone' => $model->phone,
            'address' => $model->address,
            'date_to_company' => $model->date_to_company,
            'status' => $model->status,
            'type' => $model->type,
            'position_type' => $model->position_type,
            'allowance' => $model->allowance,
            'indicator' => $model->indicator,
            'is_insurance' => $model->is_insurance,
            'date_to_job' => $model->date_to_job,
            'job' => $model->job,
            'date_to_job_group' => $model->date_to_job_group,
            'age' => $model->age,
            'service' => $model->service,
            'date_of_engagement' => $model->date_of_engagement,
            'education' => $model->education,
            'jg' => $model->jg,
            'actua_working_days' => $model->actua_working_days,
            'normal_retirement_date' => $model->normal_retirement_date,
            'expiry_status' => $model->expiry_status,
            'basic_salary' => $model->basic_salary,
            'housing_allowance' => $model->housing_allowance,
            'position_allowance' => $model->position_allowance,
            'last_day' => $model->last_day,
            'remarks' => $model->remarks,
            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at,
            'department_name' => optional($model->department)->name,
            'designation_name' => optional($model->designation)->name,
            'branch_name' => optional($model->branch)->name,
            'branch' => [
                'id' => $model->branch_id,
                'name' => optional($model->branch)->name,
            ],
            'salary' => $model->salaries,
        ];
    }

    public function includeUser(Employee $model)
    {
        if ($model->user) {
            return $this->item($model->user, new UserTransformer());
        }

        return $this->null();
    }

    public function includeDepartment(Employee $model)
    {
        if ($model->department) {
            return $this->item($model->department, new EmployeeDepartmentTransformer());
        }

        return $this->null();
    }

    public function includeDesignation(Employee $model)
    {
        if ($model->designation) {
            return $this->item($model->designation, new EmployeeDesignationTransformer());
        }

        return $this->null();
    }

    public function includeBankAccounts(Employee $model)
    {
        return $this->collection($model->bankAccounts, new EmployeeBankAccountTransformer());
    }

    public function includeContracts(Employee $model)
    {
        return $this->collection($model->contracts, new EmployeeContractTransformer());
    }

    public function includeTerminationAllowances(Employee $model)
    {
        return $this->collection($model->terminationAllowances, new TerminationAllowanceTransformer());
    }

    public function includeEmployeeAwards(Employee $model)
    {
        return $this->collection($model->employeeAwards, new EmployeePayrollAwardTransformer);
    }

    public function includeOvertimes(Employee $model)
    {
        return $this->collection($model->overtimes, new OvertimeTransformer());
    }

    public function includeRetaliations(Employee $model)
    {
        return $this->collection($model->retaliations, new RetaliationEmployeeTransformer());
    }
}
