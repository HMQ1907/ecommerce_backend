<?php

namespace App\Console;

use App\Console\Commands\CreateBackup;
use App\Console\Commands\DeleteBackup;
use App\Console\Commands\UpdateTypeEmployee;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Modules\Employees\Console\Commands\ContractEmployeeEndDate;
use Modules\Employees\Console\Commands\UpdateEmployeeTransfer;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command(CreateBackup::class)->dailyAt('01:00');
        $schedule->command(DeleteBackup::class)->dailyAt('01:00');
        $schedule->command(UpdateTypeEmployee::class)->dailyAt('01:00');
        $schedule->command(UpdateEmployeeTransfer::class)->dailyAt('01:00');
        $schedule->command(ContractEmployeeEndDate::class)->dailyAt('01:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
