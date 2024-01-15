<?php

namespace Modules\Payroll\Transformers;

use App\Transformers\BaseTransformer;
use Modules\Payroll\Models\SalaryGroup;

class SalaryGroupTransformer extends BaseTransformer
{
    /**
     * Include resources without needing it to be requested.
     */
    protected array $defaultIncludes = [
        'salary_components',
    ];

    /**
     * Transform the entity.
     *
     * @return array
     */
    public function transform(SalaryGroup $model)
    {
        return [
            'id' => (int) $model->id,
            'name' => $model->name,
            'salary_component_ids' => $model->components->pluck('id'),
            'employees' => $model->employee->pluck('employee_id'),
        ];
    }

    public function includeSalaryComponents(SalaryGroup $model)
    {
        return $this->collection($model->components, new SalaryComponentTransformer());
    }
}
