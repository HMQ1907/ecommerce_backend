<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public bool $allow_register;

    public bool $allow_auth_with_social;

    public string $default_role;

    public static function group(): string
    {
        return 'general';
    }
}
