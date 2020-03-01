<?php

namespace Elgndy\FileUploader;

use Elgndy\FileUploader\Events\UploadableModelHasCreated;
use Elgndy\FileUploader\Listeners\StoreTempMediaInRealPath;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        UploadableModelHasCreated::class => [
            StoreTempMediaInRealPath::class,
        ],
    ];

    public function boot()
    {
        parent::boot();
    }
}
