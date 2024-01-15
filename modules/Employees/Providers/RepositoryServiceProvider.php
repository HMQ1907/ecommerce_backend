<?php

namespace Modules\Employees\Providers;

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
        $this->app->bind(\Modules\Employees\Repositories\EmployeeRepository::class, \Modules\Employees\Repositories\EmployeeRepositoryEloquent::class);
        $this->app->bind(\Modules\Employees\Repositories\EmployeeBankAccountRepository::class, \Modules\Employees\Repositories\EmployeeBankAccountRepositoryEloquent::class);
        $this->app->bind(\Modules\Employees\Repositories\EmployeeContractRepository::class, \Modules\Employees\Repositories\EmployeeContractRepositoryEloquent::class);
        $this->app->bind(\Modules\Employees\Repositories\EmployeeTerminationAllowanceRepository::class, \Modules\Employees\Repositories\EmployeeTerminationAllowanceRepositoryEloquent::class);
        $this->app->bind(\Modules\Employees\Repositories\EmployeeTransferRepository::class, \Modules\Employees\Repositories\EmployeeTransferRepositoryEloquent::class);
        $this->app->bind(\Modules\Employees\Repositories\EmployeeAwardRepository::class, \Modules\Employees\Repositories\EmployeeAwardRepositoryEloquent::class);
        $this->app->bind(\Modules\Employees\Repositories\AwardRepository::class, \Modules\Employees\Repositories\AwardRepositoryEloquent::class);
        $this->app->bind(\Modules\Employees\Repositories\RetaliationRepository::class, \Modules\Employees\Repositories\RetaliationRepositoryEloquent::class);
    }
}
