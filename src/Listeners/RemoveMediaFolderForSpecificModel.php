<?php

namespace Elgndy\FileUploader\Listeners;

use Elgndy\FileUploader\FileUploaderManager;
use Elgndy\FileUploader\Events\UploadableModelHasDeleted;

class RemoveMediaFolderForSpecificModel
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
    public function handle(UploadableModelHasDeleted $event)
    {
        $this->fileUploaderManager->deleteModelMediaFolder($event->model);
    }

    /**
     * Handle a job failure.
     *
     * @param  UploadableModelHasCreated $event
     * @param  \Exception                $exception
     * @return void
     */
    public function failed(UploadableModelHasDeleted $event, $exception)
    {
        throw $exception;
    }
}
