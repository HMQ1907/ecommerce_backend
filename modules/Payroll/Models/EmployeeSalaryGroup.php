<?php

namespace Modules\Payroll\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Employees\Models\Employee;
use Wildside\Userstamps\Userstamps;

class EmployeeSalaryGroup extends BaseModel
{
    use HasFactory;
    use SoftDeletes, Userstamps;

    protected $guarded = ['id'];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function salaryGroup(): BelongsTo
    {
        return $this->belongsTo(SalaryGroup::class);
    }
}
