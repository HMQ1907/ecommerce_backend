<?php

namespace Modules\Payroll\Models;

use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'from_currency_code',
        'to_currency_code',
        'rate',
    ];

    public function exchangeable()
    {
        return $this->morphTo();
    }
}
