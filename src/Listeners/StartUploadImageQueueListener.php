<?php 
namespace Elgndy\FileUploader\Listeners;

use Elgndy\FileUploader\Contracts\Listeners\BaseListener;
use Elgndy\FileUploader\Providers\ImageProvider;
use Elgndy\FileUploader\Contracts\Events\EventInterface as Event;
use Elgndy\FileUploader\Tasks\UploadImageTask;

class StartUploadImageQueueListener extends BaseListener
{
    public function handle(Event $event)
    {
	$image_name = $event->get('image_name');
	$model = $event->get('model');
    	UploadImageTask::dispatch($image_name, $model);
    }
}
