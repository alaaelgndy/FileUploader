<?php

namespace Elgndy\FileUploader;

use Illuminate\Support\ServiceProvider;
use Elgndy\FileUploader\Commands\TempFilesCleaner;

class FileUploaderServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/migrations');
        $this->loadRoutesFrom(__DIR__.'/routes/api.php');
        $this->loadTranslationsFrom(__DIR__.'/lang', 'FileUploader');

        $this->publishes(
            [
            __DIR__.'/Config/elgndy_media.php' => config_path('elgndy_media.php'),
            ]
        );

        $this->publishes([
            __DIR__.'/lang' => resource_path('lang/vendor/FileUploader'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                TempFilesCleaner::class,
            ]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app->register(EventServiceProvider::class);

        $this->mergeConfigFrom(
            __DIR__.'/Config/elgndy_media.php',
            'elgndy_media'
        );
    }
}
