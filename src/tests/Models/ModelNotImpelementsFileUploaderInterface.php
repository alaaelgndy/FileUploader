<?php

namespace Elgndy\FileUploader\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class ModelNotImpelementsFileUploaderInterface extends Model
{
    protected $table = 'elgndy_modelb';
}
