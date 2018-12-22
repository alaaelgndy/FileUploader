<?php
namespace Elgndy\FileUploader\Contracts\Tasks;

/**
 * BaseTask 
 * @Todo:implement a queuable interface
 */
abstract class BaseTask
{

    abstract public function run(aray $args);
}
