<?php

namespace App\Providers;

use App\Helpers\Theme;
use App\Helpers\RegisterPermission;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(RegisterPermission $permission)
    {
        // Register User Permissions
        $permission->register();
        // Bind Theme class
        $this->app->singleton('theme', function(){
            return new Theme;
        });
    }
}
