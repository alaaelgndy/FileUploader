<?php

namespace Elgndy\FileUploader;

use Elgndy\FileUploader\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Elgndy\FileUploader\Services\MediaMoverService;
use Elgndy\FileUploader\Services\MediaUploaderService;

class FileUploaderManager
{
    private $mediaUploaderService;

    private $mediaMoverService;


    public function __construct(
        MediaUploaderService $mus,
        MediaMoverService $mms
    ) {
        $this->mediaUploaderService = $mus;
        $this->mediaMoverService = $mms;
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

    public function storeTempMediaInRealPath(Model $model, string $tempMedia): array
    {
        $validated = $this->mediaMoverService->validateBeforeMove([
            'model' => $model,
            'tempMedia' => $tempMedia
        ]);

        $moved = $validated->move();

        $validated->saveInDb();

        return [
            'filePath' => $moved,
            'baseUrl' => Storage::url('/')
        ];
    }

    private function getTempPath(): string
    {
        return config('elgndy_media.temp_path', 'temp/');
    }
}
