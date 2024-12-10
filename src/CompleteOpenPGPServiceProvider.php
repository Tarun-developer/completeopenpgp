<?php

namespace CompleteOpenPGP;

use Illuminate\Support\ServiceProvider;

class CompleteOpenPGPServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Bind our classes to the Laravel container
        $this->app->singleton(PGPHandler::class, function ($app) {
            return new PGPHandler();
        });

        $this->app->singleton(PGPKeyManager::class, function ($app) {
            return new PGPKeyManager();
        });
    }

    public function boot()
    {
        // Optionally, publish config file
        $this->publishes([
            __DIR__ . '/../config/completeopenpgp.php' => config_path('completeopenpgp.php'),
        ]);
    }
}

