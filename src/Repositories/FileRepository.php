<?php
namespace Elgndy\FileUploader\Repositories;

use Elgndy\FileUploader\Contracts\Db\BaseRepository;
use Elgndy\FileUploader\Models\File;
use Elgndy\FileUploader\Contracts\Exceptions\DomainRecordNotFoundException;
use Elgndy\FileUploader\Contracts\Db\TinyIntStatusTrait;

class FileRepository extends BaseRepository
{	
    use TinyIntStatusTrait;


    /**
     * file saved in db and in fs
     */
    const STATUS_DEFAULT = 0;

    /**
     * file uploaded on storage like (S3)
     */
    const STATUS_UPLOADED = 1;

    /**
     * file resized and every thing going well 
     */
    const STATUS_RESIZED = 2;


    /**
     * @var Object Elgndy\FileUploader\Models\File $model
     */
    protected $model;


    public function __construct(File $model)
    {
	$this->model = $model;
    }


    /*
     * @param String $fileName
     * @param String $model
     * @param Int $rowId
     *
     * @return object  Elgndy\FileUploader\Models\File
     */
    public function store(string $fileName, int $rowId , string $model)
    {
	$data['file_path'] = $fileName;
	$data['related_id'] = $rowId;
	$data['related_model'] = $model;
	$data['file_status'] = $this->getStatusValue('Default');
	return $this->create($data);
    }



    /*
     * @param String $file_name
     * @param String $new_status
     *
     * @return object  Elgndy\FileUploader\Models\File
     */
    public function updateStatus(string $file_name, string $new_status)
    {
	return $this->model->where([
	    'file_path' => $file_name, 
	])->update(['file_status' => $this->getStatusValue($new_status)]);
    }



    /*
     * @param String $file_name
     *
     * @return String $status
     */
    public function getFileStatus(string $file_name)
    {
	return $this->getStatusAttribute($this->findOneBy(
	    'file_path' , $file_name
	)->file_status);
    }


    /*
     * @param String $related_model_name
     * @param Int $related_id
     *
     * @return bool 
     */
    public function deleteFile(string $related_model_name, int $related_id)
    {
	return $this->model->where([
	    'related_model' => $related_model_name, 
	    'related_id' => $related_id
	])->delete(); 
    }



}
