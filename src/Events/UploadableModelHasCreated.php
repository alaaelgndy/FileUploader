<?php

namespace Elgndy\FileUploader\Events;

use Illuminate\Database\Eloquent\Model;

class UploadableModelHasCreated
{
    public $model;

    public $tempPath;

    public function __construct(Model $model, string $tempPath)
    {
        $this->model = $model;
        $this->tempPath = $tempPath;
    }
}
