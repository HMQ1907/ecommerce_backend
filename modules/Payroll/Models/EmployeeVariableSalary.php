<?php

namespace Modules\Payroll\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeVariableSalary extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'variable_component_id',
        'variable_value',
        'current_value',
        'adjustment_type',
    ];

    public function component(): BelongsTo
    {
        return $this->belongsTo(SalaryComponent::class, 'variable_component_id');
    }
}
