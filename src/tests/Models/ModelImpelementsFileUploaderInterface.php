<?php

namespace Elgndy\FileUploader\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Elgndy\FileUploader\Contracts\FileUploaderInterface;

class ModelImpelementsFileUploaderInterface extends Model implements FileUploaderInterface
{
    protected $table = 'elgndy_mediaa';

    public function getMediaTypesWithItsOwnValidationRules(): array
    {
        return [
            'images' => [
                'png',
                'jpg',
                'jpeg',
            ],
            'national_id' => [
                'pdf'
            ]
        ];
    }
}
