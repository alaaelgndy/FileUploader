<?php

namespace Elgndy\FileUploader\Tests\Traits;

use Illuminate\Support\Facades\Storage;
use Elgndy\FileUploader\Tests\Models\ModelImplementsFileUploaderInterface;

trait RemoveCreatedFiles 
{
    public function tearDown(): void
    {
        Storage::deleteDirectory('temp/'. (new ModelImplementsFileUploaderInterface())->getTable());
        Storage::deleteDirectory((new ModelImplementsFileUploaderInterface())->getTable());
    }
}