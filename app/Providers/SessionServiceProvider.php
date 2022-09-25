<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application;

/**
 * @property Application $app
 */
class SessionServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            \Illuminate\Session\SessionManager::class, 
            fn () => $this->app->loadComponent('session', \Illuminate\Session\SessionServiceProvider::class, 'session'),
        );
        
        $this->app->singleton(
            'session.store', 
            fn () => $this->app->loadComponent('session', \Illuminate\Session\SessionServiceProvider::class, 'session.store'),
        );
    }
}