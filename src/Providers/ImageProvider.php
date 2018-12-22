<?php 
namespace Elgndy\FileUploader\Providers;

use Elgndy\FileUploader\Contracts\Providers\ModelProvider;
use Elgndy\FileUploader\Repositories\FileRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Storage;
use Elgndy\FileUploader\Contracts\Exceptions\DomainRecordNotFoundException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Elgndy\FileUploader\Services\ResizeImageService;
use Illuminate\Database\Eloquent\Model as BaseModel;


class ImageProvider extends ModelProvider
{
	/**
	 * @var Object O3L\Db\Repositories\FileRepository $image_repository
	 */
	protected $image_repository;

	/**
	 * @var Object Illuminate\Support\Facades\Storage $storage
	 */
	protected $storage;

	/**
	 * @var Object Illuminate\Filesystem\Filesystem $file_system
	 */
	protected $file_system;

	/**
	 * @var Object O3L\Services\ResizeImageService $resize_image
	 */
	protected $resize_image;

	public function __construct(
		FileRepository $ir ,
		Storage $storage ,
		Filesystem $fs,
		ResizeImageService $ris
	)
	{
		$this->image_repository = $ir;
		$this->storage = $storage;
		$this->file_system = $fs;
		$this->resize_service = $ris;
	}

	/**
	 * @param Object Symfony\Component\HttpFoundation\File\UploadedFile $image
	 * @param String $path
	 * @return String created file in FS
	 */
	public function storeImage(UploadedFile $image , $path)
	{
	    $fileName = $this->fakeName($image);
	    try {
		$move = $image->move($path, $fileName);
	    } catch (FileException $e) {
		throw new DomainRecordNotFoundException($e->getMessage());
	    } catch (FileNotFoundException $e) {
		throw new DomainRecordNotFoundException($e->getMessage());
	    }
	    if (!$move) {
		throw new DomainRecordNotFoundException("unknown error in save image in file system");
	    }
	    return $fileName;
	}

	/**
	 * @param String $filefullpath
	 * @param Int $rowId
	 * @param Illuminate\Database\Eloquent\Model $model
	 * @return Int pivot id
	 */
	public function saveInDB(string $fileFullPath ,int $rowId, BaseModel $model)
	{
	    $fileName = $this->file_system->basename($fileFullPath);
	    $store = $this->image_repository->store($fileName , $rowId, $model->getTable());
	    if (!$store) {
		$this->removeFile($fileFullPath);
		throw new DomainRecordNotFoundException("unknown error in save file in database");
	    }
	    return $store;
	}

	/*
	 * remove file depend on stored disk
	 * @param String $fullFilePath
	 */
	public function removeFile(string $fullFilePath)
	{
	    if($this->file_system->exists($fullFilePath))
	    {
		return	$this->file_system->delete($fullFilePath); 
	    }
	    return;			
	}

	/**
	 * @param String $image
	 * @return new name of image
	 */
	private function fakeName($image)
	{
		return  md5(date('Y-m-d H:i:s')) . '.' . $image->getClientOriginalExtension();
	}

	/**
	 * @param String $local_image_path
	 * @param String $disk
	 * @return Boolean
	 */
	public function uploadImage($local_image_path , $disk)
	{
		try {
			$local_image_content = $this->file_system->get($local_image_path);
			$local_image_name = $this->file_system->basename($local_image_path);
		} catch (FileNotFoundException $e) {
			throw new DomainRecordNotFoundException($e->getMessage());
		}
		$uploaded = $this->storage::disk($disk)->put($local_image_name , $local_image_content);

		return ($uploaded) ? true : false;
	}

	/**
	 * @param Strign $local_image_path
	 * @param Array $sizes
	 * @return Array of Images
	 */
	public function resizeImage($local_image_path , array $sizes)
	{
		if (count($sizes) == 0) {
			return;
		}
		try {
			$local_image_name = $this->file_system->basename($local_image_path);
			$local_path = $this->file_system->dirname($local_image_path);
		} catch (FileNotFoundException $e) {
			throw new DomainRecordNotFoundException($e->getMessage());
		}

		return $this->resize_service->resize($local_image_name , $local_path , $sizes);
	}

	/**
	 * @param Array $images
	 * @param String $disk
	 * @return Boolean
	 */
	public function uploadImagesAfterResize(array $images , $disk)
	{
		for ($i = 0; $i < count($images); $i++) {
			$this->uploadImage($images[$i]->dirname . '/' . $images[$i]->basename , $disk);
		}
		return true;
	}

	/**
	 * @param String $image 
	 * @param Array $extentions
	 * @param Int $max_size
	 * @return Void
	 */
	public function validateFile($image , array $extensions , int $max_size)
	{
		if (!in_array($image->getClientOriginalExtension(),$extensions)) 
		{
			throw new DomainRecordNotFoundException('this file extension not supported!');
		}
		if ($image->getClientSize() > $max_size) 
		{
		    throw new DomainRecordNotFoundException('your file is greater than ' . $max_size . '!');
		}
		return;
	}

	/**
	 * @param String $image_name
	 * @param String $status
	 * @return Obj Models\Image
	 */
	public function updateStatus(string $image_name ,string $status)
	{
	    return $this->image_repository->updateStatus($image_name , $status);
	}


	public function getImage(string $image_name , string $local_url , string $cloud_url ,$size)
	{
		$status = $this->image_repository->getFileStatus($image_name);
		if ($status == 'Default') 
		{
		    return $this->getImageUrl($image_name , $local_url);
		} 
		elseif ($status == 'Uploaded') 
		{
		    return $this->getImageUrl($image_name , $cloud_url);
		}
		elseif ($status == 'Resized')
		{
			return $this->getImageUrl($image_name , $cloud_url , $size);
		}
	}

	private function getImageUrl(string $image_name ,string $url , $size = null)
	{
		return (!is_null($size)) ?  $url . '/' . $size .'_' .$image_name : $url . '/' . $image_name;
	}
}
