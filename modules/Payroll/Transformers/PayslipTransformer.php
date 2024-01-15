<?php

namespace Modules\Payroll\Transformers;

use App\Transformers\BaseTransformer;
use Modules\Employees\Transformers\EmployeeTransformer;
use Modules\Payroll\Models\SalarySlip;

class PayslipTransformer extends BaseTransformer
{
    /**
     * Include resources without needing it to be requested.
     */
    protected array $defaultIncludes = [
        'employee',
    ];

    /**
     * Transform the entity.
     *
     * @return array
     */
    public function transform(SalarySlip $model)
    {
        return [
            'id' => (int) $model->id,
            'employee_id' => $model->employee_id,
            'currency_code' => $model->employee->currency_code,
            'salary_group_id' => $model->salary_group_id,
            'salary_from' => $model->salary_from,
            'salary_to' => $model->salary_to,
            'paid_on' => $model->paid_on,
            'status' => $model->status,
            'salary_json' => $model->salary_json,
            'extra_json' => $model->extra_json,
            'monthly_salary' => $model->monthly_salary,
            'net_salary' => $model->net_salary,
            'gross_salary' => $model->gross_salary,
            'total_retirement_fund' => $model->employee->total_retirement_fund,
        ];
    }

    public function includeEmployee(SalarySlip $model)
    {
        if ($model->employee) {
            return $this->item($model->employee, new EmployeeTransformer());
        }

        return $this->null();
    }
}
