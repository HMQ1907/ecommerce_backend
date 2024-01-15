<?php

namespace Modules\Employees\Transformers;

use App\Transformers\BaseTransformer;
use Modules\Branches\Transformer\BranchTransformer;
use Modules\Departments\Transformers\DepartmentTransformer;
use Modules\Designations\Transformers\DesignationTransformer;
use Modules\Employees\Models\EmployeeTransfer;

class EmployeeTransferTransformer extends BaseTransformer
{
    /**
     * Include resources without needing it to be requested.
     */
    protected array $defaultIncludes = [
        'employee',
        'from_branch',
        'to_branch',
        'from_designation',
        'to_designation',
        'from_department',
        'to_department',
    ];

    /**
     * Transform the Employee entity.
     *
     * @return array
     */
    public function transform(EmployeeTransfer $model)
    {
        return [
            'id' => $model->id,
            'employee_id' => $model->employee_id,
            'description' => $model->description,
            'transfer_date' => $model->transfer_date,
            'notice_date' => $model->notice_date,
            'job' => $model->job,
            'new_salary' => $model->new_salary,
            'new_position_allowance' => $model->new_position_allowance,
            'created_by' => $model->created_by,
            'updated_by' => $model->updated_by,
        ];
    }

    public function includeEmployee(EmployeeTransfer $model)
    {
        if ($model->employee) {
            return $this->item($model->employee, new EmployeeTransformer());
        }

        return $this->null();
    }

    public function includeFromBranch(EmployeeTransfer $model)
    {
        if ($model->fromBranch) {
            return $this->item($model->fromBranch, new BranchTransformer());
        }

        return $this->null();
    }

    public function includeToBranch(EmployeeTransfer $model)
    {
        if ($model->toBranch) {
            return $this->item($model->toBranch, new BranchTransformer());
        }

        return $this->null();
    }

    public function includeFromDesignation(EmployeeTransfer $model)
    {
        if ($model->fromDesignation) {
            return $this->item($model->fromDesignation, new DesignationTransformer());
        }

        return $this->null();
    }

    public function includeToDesignation(EmployeeTransfer $model)
    {
        if ($model->toDesignation) {
            return $this->item($model->toDesignation, new DesignationTransformer());
        }

        return $this->null();
    }

    public function includeFromDepartment(EmployeeTransfer $model)
    {
        if ($model->fromDepartment) {
            return $this->item($model->fromDepartment, new DepartmentTransformer());
        }

        return $this->null();
    }

    public function includeToDepartment(EmployeeTransfer $model)
    {
        if ($model->toDepartment) {
            return $this->item($model->toDepartment, new DepartmentTransformer());
        }

        return $this->null();
    }
}
