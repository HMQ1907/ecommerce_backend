<?php

namespace Modules\Designations\Transformers;

use App\Transformers\BaseTransformer;
use Modules\Designations\Models\Designation;
use Modules\Employees\Transformers\EmployeeTransformer;

class DesignationTransformer extends BaseTransformer
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

    public function includeEmployees(Designation $designation)
    {
        if ($designation->employees) {
            return $this->collection($designation->employees, new EmployeeTransformer());
        }

        $this->null();
    }
}
