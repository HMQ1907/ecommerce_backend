<?php

namespace Modules\Teams\Providers;

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
        $this->app->bind(\Modules\Teams\Repositories\TeamRepository::class, \Modules\Teams\Repositories\TeamRepositoryEloquent::class);
    }
}
