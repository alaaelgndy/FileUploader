<?php
namespace Elgndy\FileUploader\Contracts\Db;

use Elgndy\FileUploader\Contracts\Db\RepositoryInterface;
use Illuminate\Support\Facades\DB;

/**
 * BaseReposity
 * Todo: implement a magic method Ex:findByName or findByDate
 * Todo: implement an overloading methid to accept more fields and values
 * Todo: implement more comparison model EX:(field, '>/</>=/<=', value)
 * Todo: implement aggreagating methods EX:('count', 'max', 'min')
 * Todo: implement JSON query helper
 */
abstract class BaseRepository implements RepositoryInterface
{
    protected $model;

    /**
     * get Current Model Instace
     * @return Object
     */
    public function model()
    {
        return $this->model;
    }

    /**
     * Return Fresh Model
     * @return Illuminate\Database\Eloquent\Model
     */
    public function new(array $data=[])
    {
        return $this->model->newInstance($data);
    }

    /**
     * Return all records
     * @param  array   $columns [selected columns]
     * @param  mixed[string/array] $orderBy
     * @return Collection
     */
    public function all($columns=array('*'), $orderBy=false)
    {
        $query = $this->model;
        $query = $this->setSelect($query, $columns);
        $query = $this->setOrderBy($query, $orderBy);
        return $query->all();
    }

    /**
     * Paginate Records
     * @param  integer $per_page [Limit]
     * @param  integer $page     [Offest]
     * @param  array   $columns  [selected columns]
     * @param  boolean $orderBy
     * @return Collection
     */
    public function paginate($per_page=10, $page=1, $columns=array('*'), $orderBy=false)
    {
        $offset = $page * $per_page;
        $query = $this->model;
        $query = $this->setOffset($query, $offset);
        $query = $this->setLimit($query, $per_page);
        $query = $this->setSelect($query, $columns);
        $query = $this->setOrderBy($query, $orderBy);
        return $query->get();
    }

    /**
     * Create Model from array data
     * @param  array  $data
     * @return Object
     */
    public function create(array $data=[])
    {
        return $this->model->create($data);
    }

    /**
     * Update Model By ID from array data
     * @param  array  $data
     * @return Object
     */
    public function update($id, array $data)
    {
        return $this->model->find($id)->update($data);
    }

    /**
     * Delete Model By Id
     * @param  Integer $id
     * @param  boolean $soft [Soft Delete is Default]
     */
    public function delete($id, $soft=true)
    {
        $query = $this->model->find($id);
        if($soft){
            return $query->delete();
        }
        return $query->destroy();
    }

    public function find($id, $columns=array('*'), $orderBy=false, $limit=false)
    {
        return $this->findBy('id', $id, $columns, $orderBy, $limit);
    }

    public function findOne($id, $columns=array('*'))
    {
        return $this->find($id, $columns)->first();
    }

    public function findBy($field, $value, $columns=array('*'), $orderBy=false, $limit=false, $offset=false)
    {
        $offset = $offset * $limit;
        $query = $this->prepareQuery([[$field ,$value]]);
        $query = $this->setSelect($query, $columns);
        $query = $this->setOrderBy($query, $orderBy);
        $query = $this->setOffset($query, $offset);
        $query = $this->setLimit($query, $limit);
        return $query->get();
    }

    public function findByMany(array $fields, $columns=array('*'), $orderBy=false, $limit=false, $offset=false)
    {
        $offset = $offset * $limit;
        $query = $this->prepareQuery($fields);
        $query = $this->setSelect($query, $columns);
        $query = $this->setOrderBy($query, $orderBy);
        $query = $this->setOffset($query, $offset);
        $query = $this->setLimit($query, $limit);
        return $query->get();
    }

    public function findOneByMany(array $fields, $columns=array('*'), $orderBy=false, $limit=false, $offset=false)
    {
        return $this->findByMany($fields, $columns, $orderBy, $limit, $offset)->first();
    }


    public function findOneBy($field, $value, $columns=array('*'))
    {
        $query = $this->prepareQuery([[$field ,$value]]);
        $query = $this->setSelect($query, $columns);
        return $query->first();
    }

    /**
     * Laravel DB Raw Query Builder
     * @see https://laravel.com/docs/5.5/queries
     * @param String|null $table
     * @return Illuminate\Database\Query\Builder
     */
    public function rawQuery($table = null)
    {
        $table = ($table) ? $table : $this->model->getTable();
        return DB::table($table);
    }

    /**
     * Prepare the select queries 
     * @example One Field Select : [['username' , 'john doe']]
     * @example One Field Select : [['username' , '=', 'john doe']]
     * @example Mutli Field Select : [['username' , '=', 'john doe'], ['age' , '>', '25']]
     * @param  array  $fields
     * @return Query
     */
    private function prepareQuery(array $fields)
    {
        $query = $this->model->query();

        foreach($fields as $field){

            if(count($field) == 3){
                $key = $field[0];
                $operator = $field[1];
                $value = $field[2];
                $query = $this->model->where($key, $operator, $value);

            }
            else if(count($field) == 2){
                $key = $field[0];
                $value = $field[1];
                $query->where($key, $value);
            }
        }

        return $query;
    }

    /**
     * Builds and concat OrderBy Query
     * @example OrderBy : String ('age') Default ASC
     * @example OrderBy : Array (['age'=>'asc','name'=>'desc']) Default ASC
     * @param  QueryObject         $query
     * @param  mixed[string/array] $orderBy
     * @return QueryObject
     */
    private function setOrderBy($query, $orderBy){
       if($orderBy){
            if(is_string($orderBy)){
                $query->orderBy($orderBy, 'asc');
            }
            if(is_array($orderBy)){
                foreach ($orderBy as $key => $value) {
                    $query->orderBy($key, $value);
                }
            }
       }
       return $query;
    }

    /**
     * Builds and concat Select Query
     * @param  QueryObject         $query
     * @param  mixed[string/array] $orderBy
     * @return QueryObject
     */
    private function setSelect($query,array $columns){
        if(isset($columns) && $columns[0] != '*'){
            $query->select($columns);
        }
        return $query;
    }

    private function setLimit($query, $limit)
    {
       if($limit && is_int($limit)){
            $query->limit($limit);
        }
        return $query;
    }

    private function setOffset($query, $offset)
    {
       if($offset && is_int($offset)){
            $query->offset($offset);
        }
        return $query;
    }
}
