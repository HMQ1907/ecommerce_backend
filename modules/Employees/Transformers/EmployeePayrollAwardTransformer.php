<?php

namespace Modules\Employees\Transformers;

use App\Transformers\BaseTransformer;
use Modules\Employees\Models\EmployeeAward;

class EmployeePayrollAwardTransformer extends BaseTransformer
{
    /**
     * Transform the Employee entity.
     *
     * @return array
     */
    public function transform(EmployeeAward $model)
    {
        return [
            'id' => (int) $model->id,
            'employee_id' => $model->employee_id,
            'award_id' => $model->award_id,
            'award_period' => optional($model->award)->award_period,
            'type' => $model->award->type,
            'amount' => $model->amount,
            'is_tax' => $model->is_tax,
        ];
    }
}
