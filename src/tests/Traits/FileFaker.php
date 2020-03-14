<?php

namespace Elgndy\FileUploader\Tests\Traits;

use Illuminate\Http\UploadedFile;

trait FileFaker
{
    public function fileFaker(string $extension = '.png'): UploadedFile
    {
        $extension =  $this->addDotToTheExtension($extension);

        return UploadedFile::fake()->image(md5(NOW()) . $extension);
    }

    private function addDotToTheExtension(string $extension): string
    {
        return ($extension[0] === '.') ? $extension : '.' . $extension;
    }
}
