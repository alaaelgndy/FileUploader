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
    ) {
        $this->mediaUploaderService = $mus;
        $this->mediaMoverService = $mms;
        $this->mediaDeleterService = $mds;
    }

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

    public function storeTempMediaInRealPath(Model $model, string $tempMedia): Media
    {
        $validated = $this->mediaMoverService->validateBeforeMove([
            'model' => $model,
            'tempMedia' => $tempMedia
        ]);

        $moved = $validated->move();

        return $validated->saveInDb();
    }

    public function deleteModelMediaFolder(Model $model)
    {
        $validated = $this->mediaDeleterService->validateBeforeDelete($model);
        $removed = $validated->removeFolderFromFS();

        return $validated->deleteFromDb();
    }

    private function getTempPath(): string
    {
        return config('elgndy_media.temp_path', 'temp/');
    }
}
