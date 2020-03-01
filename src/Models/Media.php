<?php

namespace Elgndy\FileUploader\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $table = 'media';

    protected $guarded = [];

    protected $attributes = [
        'metadata' => '[]',
    ];

    public function model()
    {
        return $this->morphTo();
    }
}
