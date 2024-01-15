<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateGeneralSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.allow_register', false);
        $this->migrator->add('general.allow_auth_with_social', false);
    }
}
