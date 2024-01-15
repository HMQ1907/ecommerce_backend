<?php

namespace Modules\Departments\Transformers;

use App\Transformers\BaseTransformer;
use Modules\Departments\Models\Department;
use Modules\Employees\Transformers\EmployeeTransformer;

class DepartmentTransformer extends BaseTransformer
{
    protected array $defaultIncludes = [
        'manager',
        'parent',
    ];

    /**
     * Include resources without needing it to be requested.
     */
    protected array $availableIncludes = [
        'children',
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

    public function includeManager(Department $department)
    {
        if ($department->manager) {
            return $this->item($department->manager, new EmployeeTransformer());
        }

        return $this->null();
    }

    public function includeParent(Department $department)
    {
        if (!empty($department->parent)) {
            return $this->item($department->parent, new self());
        }

        return $this->null();
    }

    public function includeChildren(Department $department)
    {
        if (count($department->children)) {
            return $this->collection($department->children, new self());
        }

        return $this->collection([], new self());
    }
}
