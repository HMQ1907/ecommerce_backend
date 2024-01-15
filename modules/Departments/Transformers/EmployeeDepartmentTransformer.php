<?php

namespace Modules\Departments\Transformers;

use App\Transformers\BaseTransformer;
use Modules\Departments\Models\Department;

class EmployeeDepartmentTransformer extends BaseTransformer
{
    /**
     * Transform the entity.
     *
     * @return array
     */
    public function transform(Department $department)
    {
        return [
            'id' => (int) $department->id,
            'name' => $department->name,
            'status' => $department->status,
        ];
    }
}
