<?php

namespace Elgndy\FileUploader\Tests\Traits;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;

trait FileFaker
{
    use WithFaker;

    public function fileFaker(string $extension = '.png'): UploadedFile
    {
        $extension =  $this->addDotToTheExtension($extension);

        return UploadedFile::fake()->image(md5($this->faker->name) . $extension);
    }

    private function addDotToTheExtension(string $extension): string
    {
        return ($extension[0] === '.') ? $extension : '.' . $extension;
    }
}
