<?php

namespace Modules\Branches\Providers;

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
        $this->app->bind(\Modules\Branches\Repositories\BranchRepository::class, \Modules\Branches\Repositories\BranchRepositoryEloquent::class);
    }
}
