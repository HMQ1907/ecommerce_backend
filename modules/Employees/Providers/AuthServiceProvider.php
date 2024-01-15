<?php

namespace Modules\Employees\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Modules\Employees\Models\Employee;
use Modules\Employees\Models\EmployeeTerminationAllowance;
use Modules\Employees\Policies\EmployeeAwardPolicy;
use Modules\Employees\Policies\EmployeePolicy;
use Modules\Employees\Policies\EmployeeTerminationPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Employee::class => EmployeePolicy::class,
        EmployeeTerminationAllowance::class => EmployeeTerminationPolicy::class,
        EmployeeAwardPolicy::class => EmployeeAwardPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    }
}
