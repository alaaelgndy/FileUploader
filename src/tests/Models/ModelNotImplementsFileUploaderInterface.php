<?php

namespace Elgndy\FileUploader\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class ModelNotImplementsFileUploaderInterface extends Model
{
    protected $table = 'elgndy_modelb';
}
