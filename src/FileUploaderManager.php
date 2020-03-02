<?php

namespace Elgndy\FileUploader;

use Elgndy\FileUploader\Models\Media;
use Elgndy\FileUploader\Services\MediaDeleterService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Elgndy\FileUploader\Services\MediaMoverService;
use Elgndy\FileUploader\Services\MediaUploaderService;

class FileUploaderManager
{
    private $mediaUploaderService;

    private $mediaMoverService;

    private $mediaDeleterService;

    public function __construct(
        MediaUploaderService $mus,
        MediaMoverService $mms,
        MediaDeleterService $mds
    )
    {
        $this->mediaUploaderService = $mus;
        $this->mediaMoverService = $mms;
        $this->mediaDeleterService = $mds;
    }

    /**
     * Upload file for the first time in temp folder.
     * The user of this function is the client side by http request.
     *
     * @param array $data
     *
     * @return array
     */
    public function uploadTheTempFile(array $data): array
    {
        $validated = $this->mediaUploaderService->validatePassedDataForTempMedia($data);

        $tempPath = $this->getTempPath();

        $uploaded = $validated->upload($tempPath);

        return [
            'filePath' => $uploaded,
            'baseUrl' => Storage::url('/')
        ];
    }

    /**
     * Move the files from temp to real path.
     * ceate the relation between the related model.
     *
     * This function will be invoked by using UploadableModelHasCreated.
     *
     * @param Model $model
     * @param string $tempMedia
     *
     * @return Media
     */
    public function storeTempMediaInRealPath(Model $model, string $tempMedia): Media
    {
        $validated = $this->mediaMoverService->validateBeforeMove(
            [
                'model' => $model,
                'tempMedia' => $tempMedia
            ]
        );

        $validated->move();

        return $validated->saveInDb();
    }

    /**
     * Delete the folder of specific model record.
     * Delete the relation.
     *
     * This function will be invoked by using UploadableModelHasDeleted.
     *
     * @param Model $model
     *
     * @return bool
     */
    public function deleteModelMediaFolder(Model $model): bool
    {
        $validated = $this->mediaDeleterService->validateBeforeDelete($model);

        $validated->removeFolderFromFS();

        return $validated->deleteFromDb();
    }

    private function getTempPath(): string
    {
        return config('elgndy_media.temp_path', 'temp/');
    }
}
