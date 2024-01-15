<?php

namespace Modules\Payroll\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Traits\TransformableTrait;
use Wildside\Userstamps\Userstamps;

class SalaryTds extends Model
{
    use HasFactory, TransformableTrait;
    use Userstamps;

    protected $fillable = [
        'salary_from',
        'salary_to',
        'salary_percent',
    ];
}
