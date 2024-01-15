<?php

namespace Modules\Attendances\Transformers;

use League\Fractal\TransformerAbstract;
use Modules\Employees\Models\Employee;

class EmployeeAttendanceTransformer extends TransformerAbstract
{
    /**
     * Include resources without needing it to be requested.
     */
    protected array $defaultIncludes = [
        'attendances',
    ];

    /**
     * Transform the Employee entity.
     *
     * @return array
     */
    public function transform(Employee $model)
    {
        $total = collect($model->attendances)->reduce(function (?int $total, $item) {
            return $total + $item->totalTime();
        }, 0);

        return [
            'employee_id' => (int) $model->id,
            'full_name' => $model->full_name,
            'avatar_url' => $model->avatar_url,
            'total' => $total,
        ];
    }

    public function includeAttendances(Employee $model)
    {
        if ($model->attendances) {
            return $this->collection($model->attendances, new AttendanceTransformer());
        }

        return $this->null();
    }
}
