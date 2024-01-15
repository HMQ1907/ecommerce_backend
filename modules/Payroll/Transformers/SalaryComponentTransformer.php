<?php

namespace Modules\Payroll\Transformers;

use League\Fractal\TransformerAbstract;
use Modules\Payroll\Models\SalaryComponent;

class SalaryComponentTransformer extends TransformerAbstract
{
    /**
     * Transform the entity.
     *
     * @return array
     */
    public function transform(SalaryComponent $model)
    {
        return [
            'id' => $model->id,
            'name' => $model->name,
            'type' => $model->type,
            'value' => $model->value,
            'value_type' => $model->value_type,
            'weight_formulate' => $model->weight_formulate,
            'is_company' => $model->is_company,
        ];
    }
}
