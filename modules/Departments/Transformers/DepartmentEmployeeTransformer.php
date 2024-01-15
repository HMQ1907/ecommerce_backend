<?php

namespace Modules\Departments\Transformers;

use App\Transformers\BaseTransformer;
use Modules\Departments\Models\Department;
use Modules\Employees\Transformers\EmployeeTransformer;

class DepartmentEmployeeTransformer extends BaseTransformer
{
    protected array $defaultIncludes = [
        'employees',
    ];

    /**
     * Transform the Customer entity.
     *
     * @return array
     */
    public function transform(Department $department)
    {
        return [
            'id' => (int) $department->id,
            'manager_id' => $department->manager_id,
            'branch_id' => $department->branch_id,
            'branch_name' => optional($department->branch)->name,
            'parent_id' => $department->parent_id,
            'parent_name' => optional($department->parent)->name,
            'name' => $department->name,
            'status' => $department->status,
            'has_child' => $department->children->count() > 0,
            'created_at' => $department->created_at,
        ];
    }

    public function includeEmployees(Department $department)
    {
        if ($department->employees) {
            return $this->collection($department->employees, new EmployeeTransformer());
        }

        return $this->null();
    }
}
