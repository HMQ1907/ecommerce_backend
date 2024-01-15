<?php

namespace Modules\Departments\Providers;

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
        $this->app->bind(\Modules\Departments\Repositories\DepartmentRepository::class, \Modules\Departments\Repositories\DepartmentRepositoryEloquent::class);
    }
}
