<?php

namespace Modules\Designations\Providers;

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
        $this->app->bind(\Modules\Designations\Repositories\DesignationRepository::class, \Modules\Designations\Repositories\DesignationRepositoryEloquent::class);

    }
}
