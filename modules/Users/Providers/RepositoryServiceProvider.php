<?php

namespace Modules\Users\Providers;

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
        $this->app->bind(\Modules\Users\Repositories\UserRepository::class, \Modules\Users\Repositories\UserRepositoryEloquent::class);
    }
}
