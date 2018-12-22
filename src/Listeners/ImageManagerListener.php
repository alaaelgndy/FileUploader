<?php 

namespace Elgndy\FileUploader\Managers\Images\Listeners;

use Elgndy\FileUploader\Contracts\Listeners\BaseListener;
use Elgndy\FileUploader\Providers\ImageProvider;
use Elgndy\FileUploader\Managers\Images\ImagesManager;
use Elgndy\FileUploader\Contracts\Events\EventInterface as Event;

class ImageManagerListener extends BaseListener
{
	public  $images_manager;

	public function __construct(ImagesManager $im)
	{
		$this->images_manager = $im;
	}

	public function handle(Event $fip)
	{
		$this->images_manager->save($fip->get('image') , $fip->get('rowId'));
	}
}
