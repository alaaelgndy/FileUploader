<?php

namespace Elgndy\FileUploader\Services;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Model;
use Elgndy\FileUploader\Contracts\FileUploaderInterface;

class MediaUploaderService
{

    /**
     * Model Object 
     * 
     * @example \App\Models\User 
     *
     * @var Model
     */
    private $model;

    /**
     * media file
     *
     * @var UploadedFile
     */
    private $media;

    /**
     * Media type
     *
     * @var string
     */
    private $mediaType;

    public function validatePassedDataForTempMedia(array $data): self
    {
        return $this->setModelObject($data['model'])
            ->isThisModelReadyForUse()
            ->isPassedMediaTypeAcceptedForThisModel($data['mediaType'])
            ->isMediaExtensionValidForThisMediaType($data['media'], $data['mediaType'])
            ->setTheProperties($data);
    }


    public function upload(string $tempPath): string
    {
        $generatedPath = $this->generateTempMediaPath($tempPath);

        return $this->media->store($generatedPath);
    }

    private function generateTempMediaPath(string $tempPath): string
    {
        $path = $tempPath;
        $path .= $this->model->getTable() . '/' . $this->mediaType;

        return $path;
    }

    private function setModelObject(string $modelName): self
    {
        $fullModelNamespace = $this->getPassedModelWithNamespace($modelName);

        if (!class_exists($fullModelNamespace)) {
            throw new Exception(trans(
                'FileUploader::exceptions.model_not_exist', 
                ['modelName' => $modelName]
            ));
        } else {
            $this->model = new $fullModelNamespace;
        }

        return $this;
    }

    private function getPassedModelWithNamespace(string $modelName): string
    {
        return config('elgndy_media.models_namespace') . $modelName;
    }

    private function isThisModelReadyForUse(): self
    {
        if (!$this->model instanceof FileUploaderInterface) {
            throw new Exception(trans(
                "FileUploader::exceptions.model_not_impelements_interface",
                ['modelName' => get_class($this->model), 'interface' => FileUploaderInterface::class]
            ));
        }

        return $this;
    }

    private function isPassedMediaTypeAcceptedForThisModel(string $mediaType): self
    {
        $allMediaType = array_keys($this->model->getMediaTypesWithItsOwnValidationRules());

        if (!in_array($mediaType, $allMediaType)) {
            $stringOfAvailableMediaTypes = implode(' or ', $allMediaType);
            throw new Exception(trans(
                "FileUploader::exceptions.not_available_media_type",
                ['modelName' => get_class($this->model), 'availableMediaTypes' => $stringOfAvailableMediaTypes]
            ));
        }

        return $this;
    }

    private function isMediaExtensionValidForThisMediaType(UploadedFile $media, string $mediaType): self
    {
        $passedMediaExtensionType = $media->getClientOriginalExtension();
        $allModelMediaTypes = $this->model->getMediaTypesWithItsOwnValidationRules();
        $availableExtensionsForPassedMediaType = $allModelMediaTypes[$mediaType];

        if (!in_array($passedMediaExtensionType, $availableExtensionsForPassedMediaType)) {
            $stringOfAvailableExtensions = implode(' or ', $availableExtensionsForPassedMediaType);
            throw new Exception(trans(
                "FileUploader::exceptions.not_available_extension",
                ['mediaType' => $mediaType, 'availableExtensions' => $stringOfAvailableExtensions]
            ));
        }

        return $this;
    }

    private function setTheProperties(array $data): self
    {
        $this->media = $data['media'];
        $this->mediaType = $data['mediaType'];

        return $this;
    }
}
