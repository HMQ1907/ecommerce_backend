<?php

namespace Modules\Overtimes\Providers;

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
        $this->app->bind(\Modules\Overtimes\Repositories\OvertimeRepository::class, \Modules\Overtimes\Repositories\OvertimeRepositoryEloquent::class);
    }
}
