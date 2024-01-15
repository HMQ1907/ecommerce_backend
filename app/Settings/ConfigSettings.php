<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class ConfigSettings extends Settings
{
    public int $shipping_discount;

    public static function group(): string
    {
        return 'config';
    }
}
