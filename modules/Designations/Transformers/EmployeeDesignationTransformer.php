<?php

namespace Modules\Designations\Transformers;

use App\Transformers\BaseTransformer;
use Modules\Designations\Models\Designation;

class EmployeeDesignationTransformer extends BaseTransformer
{
    /**
     * Transform the entity.
     *
     * @return array
     */
    public function transform(Designation $designation)
    {
        return [
            'id' => (int) $designation->id,
            'name' => $designation->name,
            'code' => $designation->code,
            'description' => $designation->description,
            'employees' => $designation->employees,
        ];
    }
}
