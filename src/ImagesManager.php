<?php 
namespace Elgndy\FileUploader;

use Elgndy\FileUploader\Providers\ImageProvider;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Database\Eloquent\Model as BaseModel;


class ImagesManager extends BaseManager 
{
    /**
     * @var Object Elgndy\FileUploader\Providers\ImageProvider
     */
    protected $image_provider;

    /**
     * @var Object Illuminate\Contracts\Config\Repository
     */
    protected $config;

    public function __construct(ImageProvider $ip , Config $config)
    {
	$this->image_provider = $ip;
	$this->config = $config;
    }

    /**
     * @param Object Symfony\Component\HttpFoundation\File\UploadedFile $image
     * @param Int $rowId
     * @param Object BaseModel $model 
     * @return String $saveImage
     */
    public function save(UploadedFile $image,int $rowId, BaseModel $model)
    {
	// validate depend on extensions in config
	$this->image_provider->validateFile($image , $this->getSupporedExtention() , $this->getMaxSize());
	// save default file in FS   
	$saveImage = $this->image_provider->storeImage($image , $this->getPath());
	// save image name in DB    
	$saveInDB =  $this->image_provider->saveInDB($this->getImageFullPath($saveImage), $rowId, $model);

	// this line will work in queue 
	$this->fireEvent("ImageHasBeenUploadedOnStorage", [
	    'image_name' => $saveImage,
	    'model' => $model,
	]);
	return $saveImage;
    }

    public function UploadOnStorage($saveImage, $model)
    {
	$this->image_provider->uploadImage($this->getImageFullPath($saveImage), $this->getDisk());
	$this->image_provider->updateStatus($saveImage , 'Uploaded');

	if($this->getResizeStatus() == 1)
	{
	    $images_after_resize = $this->image_provider->resizeImage(
		$this->getImageFullPath($saveImage), 
		$this->getResizeValues($model)
	    );
	    $this->image_provider->uploadImagesAfterResize($images_after_resize , $this->getDisk());
	    $this->image_provider->updateStatus($saveImage , 'Resized');	
	}
    }

    public function renderImage($image_name , $size=null)
    {
	return $this->image_provider->getImage($image_name , $this->getLocalUrl() , $this->getCloudUrl() ,$size);
    }

    /**
     * @return default path in FS
     */
    private function getPath()
    {
	return $this->config->get('ElgndyFileConfig.path');
    }

    /**
     * @return disk 
     */
    private function getDisk()
    {
	return $this->config->get('ElgndyFileConfig.disk');
    }

    /**
     * @return full image path in FS
     */
    private function getImageFullPath($image_name)
    {
	return $this->getPath() . '/' . $image_name;
    }

    /**
     * @return Array $extensions
     */
    private function getSupporedExtention()
    {
	return $this->config->get('ElgndyFileConfig.extensions');
    }

    /**
     * @return Int $max_size
     */
    private function getMaxSize()
    {
	return $this->config->get('ElgndyFileConfig.max_file_size');
    }

    /**
     * @param String $model 
     * @return array of sizes in this type 
     */
    private function getResizeValues($model) : Array
    {
	$all_sizes =  $this->config->get('ElgndyFileConfig.sizes');
	return  ($all_sizes[$model]) ?? $this->getDefaultSizes();
    }


    private function getCloudUrl()
    {
	return $this->config->get('ElgndyFileConfig.cloud_url');
    }
    
    
    private function getLocalUrl()
    {
	return $this->config->get('ElgndyFileConfig.local_url');
    }


    private function getResizeStatus()
    {
	return $this->config->get('ElgndyFileConfig.enable_resize'); 
    }


    private function getDefaultSizes()
    {
	return $this->config->get('ElgndyFileConfig.default_sizes');
    }
}
