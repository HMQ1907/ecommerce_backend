<?php

namespace Modules\Employees\Transformers;

use App\Transformers\BaseTransformer;
use App\Transformers\MediaTransformer;
use Modules\Employees\Models\EmployeeContract;

class EmployeeContractTransformer extends BaseTransformer
{
    /**
     * Include resources without needing it to be requested.
     */
    protected array $defaultIncludes = [
        'attachments',
    ];

    /**
     * Transform the Employee entity.
     *
     * @return array
     */
    public function transform(EmployeeContract $model)
    {
        return [
            'id' => (int) $model->id,
            'employee_id' => $model->employee_id,
            'full_name' => optional($model->employee)->full_name,
            'currency_code' => optional($model->employee)->currency_code,
            'basic_salary' => optional(optional($model->employee)->currentSalary())->current_basic_salary,
            'type' => $model->type,
            'number' => $model->number,
            'contract_file' => $model->contract_file,
            'contract_from' => $model->contract_from,
            'contract_to' => $model->contract_to,
            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at,
        ];
    }

    public function includeAttachments(EmployeeContract $employeeContract)
    {
        return $this->collection($employeeContract->media, new MediaTransformer());
    }
}
