<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Payroll\Models\EmployeeSalary;

class UpdateCurrency extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-currency';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        return EmployeeSalary::query()->whereHas('employee', function ($query) {
            $query->where('type', '<>', 'expat');
        })->update([
            'currency_code' => 'LAK',
        ]);
    }
}
