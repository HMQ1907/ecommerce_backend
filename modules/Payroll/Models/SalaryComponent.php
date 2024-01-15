<?php

namespace Modules\Payroll\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Traits\TransformableTrait;

class SalaryComponent extends Model
{
    use HasFactory, TransformableTrait;

    const EARNING_TYPE = 'earning';

    const DEDUCTION_TYPE = 'deduction';

    const BUSINESS_FEE = 1;

    const INSURANCE = 0;

    protected $fillable = [
        'name',
        'type',
        'value',
        'value_type',
        'is_company',
        'weight_formulate',
    ];
}
