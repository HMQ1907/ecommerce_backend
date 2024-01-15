<?php

namespace Modules\Payroll\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Modules\Payroll\Models\ExchangeRate;

trait HasExchangeRate
{
    public function exchangeRates(): MorphMany
    {
        return $this->morphMany(ExchangeRate::class, 'exchangeable');
    }
}
