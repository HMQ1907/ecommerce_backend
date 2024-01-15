<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class BackupSettings extends Settings
{
    public string $status;

    public string $hour_of_day;

    public int $backup_after_days;

    public int $delete_backup_after_days;

    public static function group(): string
    {
        return 'backup';
    }
}
