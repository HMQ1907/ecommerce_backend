<?php

namespace Modules\Overtimes\Transformers;

use App\Transformers\BaseTransformer;
use Modules\Overtimes\Models\Overtime;

class OvertimeTransformer extends BaseTransformer
{
    /**
     * Transform the Employee entity.
     *
     * @return array
     */
    public function transform(Overtime $model)
    {
        return [
            'id' => (int) $model->id,
            'employee_id' => $model->employee_id,
            'employee_name' => optional($model->employee)->full_name,
            'overtime_date' => $model->overtime_date,
            'rates' => $model->rates,
            'total_hrs' => $model->total_hrs,
            'total_amount' => $model->total_amount,
            'basic_salary' => optional(optional($model->employee)->currentSalary())->current_basic_salary,
            'salaries' => $model->employee?->salaries,
        ];
    }
}
