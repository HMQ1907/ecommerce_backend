<?php

namespace Modules\Roles\Providers;

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
        $this->app->bind(\Modules\Roles\Repositories\RoleRepository::class, \Modules\Roles\Repositories\RoleRepositoryEloquent::class);
        $this->app->bind(\Modules\Roles\Repositories\PermissionRepository::class, \Modules\Roles\Repositories\PermissionRepositoryEloquent::class);
    }
}
