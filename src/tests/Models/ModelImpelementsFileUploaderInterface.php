<?php

namespace Elgndy\FileUploader\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Elgndy\FileUploader\Contracts\FileUploaderInterface;
use Elgndy\FileUploader\Traits\Uploadable;

class ModelImpelementsFileUploaderInterface extends Model implements FileUploaderInterface
{
    use Uploadable;

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
