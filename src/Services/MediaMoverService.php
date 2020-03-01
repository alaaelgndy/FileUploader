<?php

namespace Elgndy\FileUploader\Services;

use Exception;
use Elgndy\FileUploader\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class mediaMoverService
{
    /**
     * media type
     *
     * @var string
     */
    private $mediaType;

    /**
     * Related model
     *
     * @var Model
     */
    private $model;

    /**
     * temp path
     *
     * @example temp/users/mediaType/mediaName.mediaExtension
     * 
     * @var string
     */
    private $fullTempPath;

    /**
     * real path
     *
     * @example users/userId/mediaType/mediaName.mediaExtension
     * 
     * @var string
     */
    private $fullRealPath;

    /**
     * our media model
     *
     * @var Model
     */
    private $mediaModel;

    public function __construct(Media $media)
    {
        $this->mediaModel = $media;
    }


    public function validateBeforeMove(array $data): self
    {
        return $this->checkTempMediaExistence($data['tempMedia'])
            ->setProperties($data);
    }

    public function move(): string
    {
        $moved = Storage::move($this->fullTempPath, $this->fullRealPath);

        throw_if(!$moved, new Exception("Could not move the media"));

        return $this->fullRealPath;
    }

    public function saveInDb()
    {
        $this->mediaModel->create([
            'model_type' => get_class($this->model),
            'model_id' => $this->model->id,
            'file_path' => $this->fullRealPath,
            'file_type' => $this->getMediaTypeFromTempPath()
        ]);
    }

    private function checkTempMediaExistence(string $tempMedia): self
    {
        $check = Storage::exists($tempMedia);

        throw_if(!$check, new Exception("This file is not exist " . $tempMedia));

        return $this;
    }

    private function setProperties(array $data): self
    {
        $this->fullTempPath = $data['tempMedia'];
        $this->model = $data['model'];
        $this->mediaType = $this->getMediaTypeFromTempPath();
        $this->fullRealPath = $this->generateTheFullRealPath();

        return $this;
    }

    private function getMediaTypeFromTempPath(): string
    {
        try {
            $pathInArray = explode('/', $this->fullTempPath);
            $mediaType = $pathInArray[2];

            return $mediaType;
        } catch (\Throwable $th) {
            throw new Exception("Could not extract the media type from the temp path");
        }
    }

    private function generateTheFullRealPath(): string
    {
        $table = $this->model->getTable();
        $model_id = $this->model->id;
        $mediaName = $this->getMediaNameFromTempPath();

        return $table . '/' . $model_id . '/' . $this->mediaType . '/' . $mediaName;
    }

    private function getMediaNameFromTempPath(): string
    {
        try {
            $pathInArray = explode('/', $this->fullTempPath);
            $mediaName = end($pathInArray);

            return $mediaName;
        } catch (\Throwable $th) {
            throw new Exception("Could not extract the media name from the temp path");
        }
    }
}
