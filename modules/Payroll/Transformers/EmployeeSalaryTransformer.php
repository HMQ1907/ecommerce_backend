<?php

namespace Modules\Payroll\Transformers;

use App\Transformers\BaseTransformer;
use Modules\Employees\Models\Employee;

class EmployeeSalaryTransformer extends BaseTransformer
{
    /**
     * Resources that can be included if requested.
     */
    protected array $defaultIncludes = [
        'salary',
    ];

    /**
     * Transform the entity.
     *
     * @return array
     */
    public function transform(Employee $model)
    {
        return [
            'id' => (int) $model->id,
            'employee_name' => $model->full_name,
            'currency_code' => $model->currency_code,
        ];
    }

    public function includeSalary(Employee $model)
    {
        if ($model->currentSalary()) {
            return $this->item($model->currentSalary(), new CurrentSalaryTransformer());
        }

        return $this->null();
    }
}
