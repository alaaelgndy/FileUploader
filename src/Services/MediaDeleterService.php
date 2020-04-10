<?php

namespace Elgndy\FileUploader\Services;

use Exception;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Collection;
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

        throw_if(!$check, new Exception(trans(
            'FileUploader::exceptions.model_not_impelements_interface',
            ['modelName' => get_class($model), 'interface' => FileUploaderInterface::class]
        )));

        return $this->setProperties($model);
    }

    public function removeFolderFromFS(): bool
    {
        $folder = $this->getTheFolder();

        return Storage::deleteDirectory($folder);
    }

    public function deleteFromDb(): Collection
    {
        return $this->model->media->each->delete();
    }

    public function cleanTempFolder(): int
    {
        $maxExistanceTimeToRemove = config('elgndy_media.clean_temp', 1);

        $allTempFiles = Storage::allFiles(config('elgndy_media.temp_path'));

        $deletedCounter = 0;
        $timeNow = Carbon::now();

        foreach ($allTempFiles as $file) {
            $creationTime = Carbon::createFromTimestamp(Storage::lastModified($file));

            $diff = $timeNow->diffInMinutes($creationTime);

            if ($diff >= $maxExistanceTimeToRemove) {
                Storage::delete($file);
                ++$deletedCounter;
            }
        }

        return $deletedCounter;
    }

    private function getTheFolder(): string
    {
        return $this->model->getTable().DIRECTORY_SEPARATOR.$this->model->id;
    }

    private function setProperties(Model $model): self
    {
        $this->model = $model;

        return $this;
    }
}
