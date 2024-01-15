<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('attendance.lat', 0);
        $this->migrator->add('attendance.lng', 0);
        $this->migrator->add('attendance.radius', 0);
        $this->migrator->add('attendance.ips', '127.0.0.1');
        $this->migrator->add('attendance.work_time', [
            'start' => '07:30',
            'end' => '17:00',
        ]);
    }
};
