<?php

namespace Modules\Payroll\Transformers;

use App\Transformers\BaseTransformer;
use Modules\Employees\Models\Employee;

class EmployeeTransformer extends BaseTransformer
{
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
            'first_name' => $model->first_name,
            'last_name' => $model->last_name,
            'full_name' => $model->full_name,
            'currency_code' => $model->currency_code,
            'salaries' => $model->salaries,
            'variable_salaries' => $model->variableSalaries,
            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at,
        ];
    }
}
