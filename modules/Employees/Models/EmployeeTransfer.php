<?php

namespace Modules\Employees\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Branches\Models\Branch;
use Modules\Departments\Models\Department;
use Modules\Designations\Models\Designation;
use Wildside\Userstamps\Userstamps;

class EmployeeTransfer extends Model
{
    use HasFactory, SoftDeletes;
    use Userstamps;

    protected $fillable = [
        'employee_id',
        'from_branch_id',
        'to_branch_id',
        'from_department_id',
        'to_department_id',
        'from_designation_id',
        'to_designation_id',
        'description',
        'transfer_date',
        'notice_date',
        'job',
        'new_salary',
        'new_position_allowance',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class)->withoutGlobalScope('branch');
    }

    public function fromBranch()
    {
        return $this->belongsTo(Branch::class, 'from_branch_id');
    }

    public function toBranch()
    {
        return $this->belongsTo(Branch::class, 'to_branch_id');
    }

    public function fromDepartment()
    {
        return $this->belongsTo(Department::class, 'from_department_id');
    }

    public function toDepartment()
    {
        return $this->belongsTo(Department::class, 'to_department_id');
    }

    public function fromDesignation()
    {
        return $this->belongsTo(Designation::class, 'from_designation_id');
    }

    public function toDesignation()
    {
        return $this->belongsTo(Designation::class, 'to_designation_id');
    }
}
