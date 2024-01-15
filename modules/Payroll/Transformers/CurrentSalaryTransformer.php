<?php

namespace Modules\Payroll\Transformers;

use App\Transformers\BaseTransformer;
use Modules\Payroll\Models\EmployeeSalary;

class CurrentSalaryTransformer extends BaseTransformer
{
    /**
     * Transform the entity.
     *
     * @return array
     */
    public function transform(EmployeeSalary $model)
    {
        return [
            'id' => (int) $model->id,
            'employee_id' => $model->employee_id,
            'basic_salary' => $model->basic_salary,
            'current_basic_salary' => $model->current_basic_salary,
            'social_security' => $model->social_security,
            'retirement_fund' => $model->retirement_fund,
            'insurance_salary' => $model->insurance_salary,
            'type' => $model->type,
            'date' => $model->date,
            'variable_salaries' => $model->variableSalaries,
        ];
    }
}
