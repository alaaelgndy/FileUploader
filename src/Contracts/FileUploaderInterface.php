<?php

namespace Elgndy\FileUploader\Contracts;

interface FileUploaderInterface
{
    public function getMediaTypesWithItsOwnValidationRules(): array;
}
