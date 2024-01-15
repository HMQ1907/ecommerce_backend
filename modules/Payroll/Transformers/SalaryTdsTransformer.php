<?php

namespace Modules\Payroll\Transformers;

use League\Fractal\TransformerAbstract;
use Modules\Payroll\Models\SalaryTds;

class SalaryTdsTransformer extends TransformerAbstract
{
    /**
     * Transform the entity.
     *
     * @return array
     */
    public function transform(SalaryTds $model)
    {
        return [
            'id' => $model->id,
            'salary_from' => $model->salary_from,
            'salary_to' => $model->salary_to,
            'salary_percent' => $model->salary_percent,
        ];
    }
}
