<?php
namespace Elgndy\FileUploader\Contracts\Db;

interface RepositoryInterface
{
    public function all($columns=array('*'));
    public function paginate($perpage=10, $page=1, $columns=array('*'));
    public function create(array $data);
    public function update($id ,array $data);
    public function delete($id, $soft=true);
    public function find($id, $columns=array('*'));
    public function findBy($field, $value, $columns=array('*'));
}
