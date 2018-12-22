<?php
namespace Elgndy\FileUploader\Contracts\Listeners;

use Elgndy\FileUploader\Contracts\Events\EventInterface as Event;

abstract class BaseListener
{
    abstract public function handle(Event $entity);
} 
