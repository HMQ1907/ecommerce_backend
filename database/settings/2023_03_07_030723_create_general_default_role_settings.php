<?php

use Modules\Roles\Models\Role;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateGeneralDefaultRoleSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.default_role', Role::USER);
    }
}
