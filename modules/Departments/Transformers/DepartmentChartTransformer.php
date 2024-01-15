<?php

namespace Modules\Departments\Transformers;

use App\Transformers\BaseTransformer;
use Modules\Departments\Models\Department;
use Modules\Employees\Transformers\EmployeeTransformer;

class DepartmentChartTransformer extends BaseTransformer
{
    protected array $defaultIncludes = [
        'manager',
    ];

    /**
     * Transform the Customer entity.
     *
     * @return array
     */
    public function transform(Department $department)
    {
        return [
            'id' => 'D-'.$department->id,
            'manager_id' => $department->manager_id,
            'parent_id' => $department->parent_id,
            'parent_name' => $department->parent ? $department->parent->name : null,
            'name' => $department->name,
            'status' => $department->status,
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
}
