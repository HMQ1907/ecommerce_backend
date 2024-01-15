<?php

namespace Modules\Payroll\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalaryGroupComponent extends BaseModel
{
    protected $guarded = ['id'];

    public function group(): BelongsTo
    {
        return $this->belongsTo(SalaryGroup::class);
    }

    public function component(): BelongsTo
    {
        return $this->belongsTo(SalaryComponent::class, 'salary_component_id');
    }
}
