<?php

namespace Elgndy\FileUploader;

use Illuminate\Support\ServiceProvider;

class FileUploaderServiceprovider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
	$this->loadMigrationsFrom(__DIR__.'/migrations');
	$this->publishes([
	    __DIR__.'/Config/ElgndyFileConfig.php' => config_path('ElgndyFileConfig.php'),
	]); 
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
	    __DIR__.'/Config/ElgndyFileConfig.php', 'ElgndyFileConfig'
	); 
    }
}
