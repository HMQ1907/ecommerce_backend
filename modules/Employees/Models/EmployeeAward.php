<?php

namespace Modules\Employees\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Payroll\Models\ExchangeRate;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Wildside\Userstamps\Userstamps;

class EmployeeAward extends BaseModel implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use Userstamps;

    protected $fillable = [
        'employee_id',
        'award_id',
        'amount',
        'amount_tax',
        'is_tax',
    ];

    protected $table = 'employee_awards';

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function award()
    {
        return $this->belongsTo(Award::class, 'award_id');
    }

    public function exchangeRates()
    {
        return $this->morphMany(ExchangeRate::class, 'exchangeable');
    }
}
