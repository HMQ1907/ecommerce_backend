<?php

namespace Modules\Payroll\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(\Modules\Payroll\Repositories\SalaryTdsRepository::class, \Modules\Payroll\Repositories\SalaryTdsRepositoryEloquent::class);
        $this->app->bind(\Modules\Payroll\Repositories\SalaryComponentRepository::class, \Modules\Payroll\Repositories\SalaryComponentRepositoryEloquent::class);
        $this->app->bind(\Modules\Payroll\Repositories\SalaryGroupRepository::class, \Modules\Payroll\Repositories\SalaryGroupRepositoryEloquent::class);
        $this->app->bind(\Modules\Payroll\Repositories\EmployeeSalaryRepository::class, \Modules\Payroll\Repositories\EmployeeSalaryRepositoryEloquent::class);
    }
}
