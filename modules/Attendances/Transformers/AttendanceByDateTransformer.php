<?php

namespace Modules\Attendances\Transformers;

use League\Fractal\TransformerAbstract;

class AttendanceByDateTransformer extends TransformerAbstract
{
    /**
     * Include resources without needing it to be requested.
     */
    protected array $defaultIncludes = [
        'attendances',
    ];

    /**
     * Transform the entity.
     *
     * @return array
     */
    public function transform($attendance)
    {
        return [
            'date' => $attendance['date'],
        ];
    }

    public function includeAttendances($attendance)
    {
        return $this->collection($attendance['attendances'], new AttendanceTransformer());
    }
}
