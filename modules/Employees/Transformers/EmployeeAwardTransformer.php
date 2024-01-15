<?php

namespace Modules\Employees\Transformers;

use App\Transformers\BaseTransformer;
use Modules\Employees\Models\EmployeeAward;

class EmployeeAwardTransformer extends BaseTransformer
{
    /**
     * Transform the Employee entity.
     *
     * @return array
     */
    public function transform(EmployeeAward $model)
    {
        $employee = $model->employee;
        $employee['basic_salary'] = optional(optional($model->employee)->currentSalary())->current_basic_salary;
        $employee['full_name'] = optional($model->employee)->full_name;

        return [
            'id' => (int) $model->id,
            'employee_id' => $model->employee_id,
            'award_id' => $model->award_id,
            'amount' => $model->amount,
            'is_tax' => $model->is_tax,
            'full_name' => $employee['full_name'],
            'currency_code' => optional($model->employee)->currency_code,
            'basic_salary' => $employee['basic_salary'],
            'employee' => $employee,
        ];
    }
}
