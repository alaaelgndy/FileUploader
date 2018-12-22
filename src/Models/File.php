<?php

namespace Elgndy\FileUploader\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $table = 'elgndy_files';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
	'file_path', 
	'file_status',
	'related_model',
	'related_id',
    ];

}
