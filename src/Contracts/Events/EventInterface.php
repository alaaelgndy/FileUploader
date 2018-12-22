<?php
namespace Elgndy\FileUploader\Contracts\Events;

interface EventInterface 
{
    public function get($key = null);
    public function fire($data);
} 
