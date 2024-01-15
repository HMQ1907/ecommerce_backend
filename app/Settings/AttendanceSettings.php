<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class AttendanceSettings extends Settings
{
    public float $lat;

    public float $lng;

    public int $radius;

    public ?string $ips;

    public array $work_time;

    public static function group(): string
    {
        return 'attendance';
    }
}
