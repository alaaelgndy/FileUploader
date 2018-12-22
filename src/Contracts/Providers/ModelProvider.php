<?php
namespace Elgndy\FileUploader\Contracts\Providers;

use Elgndy\FileUploader\Contracts\Exceptions\DomainRecordNotFoundException;

/**
 * ModelProvider
 */
abstract class ModelProvider 
{

    /**
     * Implements
     * @var O3L\Db\Contracts\BaseRepository
     */
    protected $repository;

    /**
     * Find Or Fail By Id
     * @param  Mixed $id
     * @throws DomainRecordNotFoundException
     * @return Model
     */
    protected function findOrFailById($id, $repository = null)
    {
        $repository = ($repository) ? $repository : 'repository';
        
        if($record = $this->$repository->findOne($id)){
            return $record;
        }
        
        throw new DomainRecordNotFoundException("Record Not found");
    }

    /**
     * Gets the user by filed and value
     * @param string $name
     * @param string $value
     * @throws DomainRecordNotFoundException
     * @return Models\Story
     */
    public function getDetailsBy($field, $value)
    {
        if($user = $this->repository->findOneBy($field, $value))
        {
            return $user;
        }

        throw new DomainRecordNotFoundException("User not found using {$field} = {$name}",[]);
    }

}
