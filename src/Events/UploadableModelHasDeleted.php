<?php

namespace Elgndy\FileUploader\Events;

use Illuminate\Database\Eloquent\Model;

class UploadableModelHasDeleted
{
    public $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }
}
