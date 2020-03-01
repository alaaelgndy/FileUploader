<?php

namespace Elgndy\FileUploader;

interface Uploader
{
    public function validatePassedData(array $data): self;

    public function upload(array $data): self;
}
