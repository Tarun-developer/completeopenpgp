<?php

namespace CompleteOpenPGP;

use Illuminate\Support\ServiceProvider;
use KeyManagement\PGPKeyManager;

class CompleteOpenPGPServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        // Bind the PGPKeyManager into the service container
        $this->app->singleton('completeopenpgp', function ($app) {
            return new PGPKeyManager();
        });

        // Merge default configuration
        $this->mergeConfigFrom(
            __DIR__ . '/../config/completeopenpgp.php', 'completeopenpgp'
        );
    }

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish configuration file to application config directory
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/completeopenpgp.php' => config_path('completeopenpgp.php'),
            ], 'config');
        }
    }
}
