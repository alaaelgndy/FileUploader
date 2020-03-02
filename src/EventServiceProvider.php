<?php

namespace Elgndy\FileUploader;

use Elgndy\FileUploader\Events\UploadableModelHasCreated;
use Elgndy\FileUploader\Events\UploadableModelHasDeleted;
use Elgndy\FileUploader\Listeners\StoreTempMediaInRealPath;
use Elgndy\FileUploader\Listeners\RemoveMediaFolderForSpecificModel;
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
        UploadableModelHasDeleted::class => [
            RemoveMediaFolderForSpecificModel::class
        ]
    ];

    public function boot()
    {
        parent::boot();
    }
}
