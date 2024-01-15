<?php

namespace Modules\Employees\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Wildside\Userstamps\Userstamps;

class EmployeeContract extends BaseModel implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use Userstamps;

    protected $fillable = [
        'employee_id',
        'type',
        'number',
        'contract_file',
        'contract_from',
        'contract_to',
        'created_by',
    ];

    protected $table = 'employee_contracts';

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
