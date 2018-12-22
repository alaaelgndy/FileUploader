<?php 
namespace Elgndy\FileUploader\Tasks;

use Elgndy\FileUploader\ImagesManager;
use Elgndy\FileUploader\Contracts\Tasks\QueuedTask;

class UploadImageTask extends QueuedTask
{
	public $tries = 3;

	public $timeout = 120;

	/**
     * ImagesManager
     * @var Instance
     */
    private $manager;

    /**
     * image_name
     * @var String
     */
    protected $image_name;
    
    /**
     * model
     * @var String
     */
    protected $model;


	public function __construct($image_name, $model)
	{
		$this->image_name = $image_name;
		$this->model = $model->getTable();
	}

	public function handle(ImagesManager $manager)
	{
	    try {
		$manager->UploadOnStorage($this->image_name, $this->model);
		return true;
	    } catch (\Exception $e) {
		echo $e->getMessage();
	    }
	}
}
