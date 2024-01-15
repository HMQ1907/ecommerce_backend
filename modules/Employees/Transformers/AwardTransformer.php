<?php

namespace Modules\Employees\Transformers;

use App\Transformers\BaseTransformer;
use Modules\Employees\Models\Award;

class AwardTransformer extends BaseTransformer
{
    /**
     * Include resources without needing it to be requested.
     */
    protected array $defaultIncludes = [
        'employees',
    ];

    /**
     * Transform the Employee entity.
     *
     * @return array
     */
    public function transform(Award $model)
    {
        return [
            'id' => (int) $model->id,
            'title' => $model->title,
            'award_type' => $model->award_type,
            'type' => $model->type,
            'total_amount' => $model->total_amount,
            'award_period' => $model->award_period,
            'created_at' => $model->created_at,
        ];
    }

    public function includeEmployees(Award $model)
    {
        return $this->collection($model->employeeAwards, new EmployeeAwardTransformer());
    }
}
