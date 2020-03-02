<?php

namespace Elgndy\FileUploader;

use Illuminate\Support\ServiceProvider;
use Elgndy\FileUploader\EventServiceProvider;

class FileUploaderServiceprovider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/migrations');
        $this->loadRoutesFrom(__DIR__ . '/routes/api.php');

        $this->publishes(
            [
            __DIR__ . '/Config/elgndy_media.php' => config_path('elgndy_media.php'),
            ]
        );
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->register(EventServiceProvider::class);

        $this->mergeConfigFrom(
            __DIR__ . '/Config/elgndy_media.php',
            'elgndy_media'
        );
    }
}
