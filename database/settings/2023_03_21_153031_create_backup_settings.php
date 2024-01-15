<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('backup.status', 'active');
        $this->migrator->add('backup.hour_of_day', '01:00');
        $this->migrator->add('backup.backup_after_days', 1);
        $this->migrator->add('backup.delete_backup_after_days', 7);
    }
};
