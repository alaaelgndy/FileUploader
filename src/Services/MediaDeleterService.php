<?php

namespace Elgndy\FileUploader\Services;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Elgndy\FileUploader\Contracts\FileUploaderInterface;

class MediaDeleterService
{
    /**
     * the model which we need to remove it's media.
     *
     * @var Model
     */
    private $model;

    public function validateBeforeDelete(Model $model): self
    {
        $check = $model instanceof FileUploaderInterface;

        throw_if(!$check, new Exception("The model does not impelement " . FileUploaderInterface::class));

        return $this->setProperties($model);
    }

    public function removeFolderFromFS(): bool
    {
        $folder = $this->getTheFolder();

        return Storage::deleteDirectory($folder);
    }

    public function deleteFromDb()
    {
        $this->model->media->each->delete();
    }

    private function getTheFolder(): string
    {
        return $this->model->getTable() . '/' . $this->model->id;
    }



    private function setProperties(Model $model): self
    {
        $this->model = $model;
        return $this;
    }
}
