<?php

namespace Elgndy\FileUploader\Listeners;

use Elgndy\FileUploader\FileUploaderManager;
use Elgndy\FileUploader\Events\UploadableModelHasCreated;

class StoreTempMediaInRealPath
{

    private $fileUploaderManager;

    public function __construct(FileUploaderManager $fum)
    {
        $this->fileUploaderManager = $fum;
    }

    /**
     * Handle the event.
     *
     * @param  UploadableModelHasCreated $event
     * @return void
     */
    public function handle(UploadableModelHasCreated $event): void
    {
        $this->fileUploaderManager->storeTempMediaInRealPath($event->model, $event->tempPath);
    }

    /**
     * Handle a job failure.
     *
     * @param  UploadableModelHasCreated $event
     * @param  \Exception $exception
     * @return void
     */
    public function failed(UploadableModelHasCreated $event, $exception): void
    {
        throw $exception;
    }
}
