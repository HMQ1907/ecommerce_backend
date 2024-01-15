<?php

namespace Modules\Attendances\Providers;

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
        $this->app->bind(\Modules\Attendances\Repositories\AttendanceRepository::class, \Modules\Attendances\Repositories\AttendanceRepositoryEloquent::class);
    }
}
