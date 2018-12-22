<?php
namespace Elgndy\FileUploader\Managers;

use Illuminate\Validation\Factory;

/**
 * BaseManagers shared functionality
 */
abstract class BaseManager
{
    /**
     * Working Manager's namspeace
     * @var string
     */
    private $namespace;

    /**
     * Manager's validators instances holder
     * @var \O3L\Contracts\CustomValidation\CustomValidator
     * @var array
     */
    private $validators;

    /**
     * Manager's events instances holder
     * @var \O3L\Contracts\Events\BaseEvent
     * @var array
     */
    private $events;

    /**
     * Manager's tasks instances holder
     * @var \O3L\Contracts\Managers\Tasks\BaseTask
     * @var array
     */
    private $tasks;

    /**
     * Other Autoloaded Managers Instances 
     * @var \O3L\Contracts\Managers\BaseManager
     * @var array
     */
    private $assiocated_managers;


    /**
     * runs corresponding Validtor matching the method name
     * Example: function signup($data) { $this->validate()} calls SignupValidator
     * @param  array   $input           [description]
     * @param  boolean $customValidator [description]
     * @throws DomainValidationException
     */
    
    public function validate(array $input, $customValidator=false)
    {
        $validator_name = ($customValidator) ? $customValidator : ucfirst(debug_backtrace()[1]['function']).'Validator';  
        $this->resolveValidator($validator_name)->validate($input);
    }

    /**
     * Fires and Attach data to events in Events Folder
     * @param  String $event_name 
     * @param  array  $data       
     * @return BaseEvent             
     */
    public function fireEvent($event_name, $data=[])
    {
        return $this->resolveEvent($event_name)->fire($data);
    }

    /**
     * Resolve and run Task and passes Args to it in Tasks folder
     * @param  String $task_name 
     * @param  array  $args      
     * @return BaseTask          
     */
    public function execute($task_name, $args=[])
    {
        return $this->resolveTask($task_name)->run($args);
    }

    /**
     * Resolve transform and excutes data transforming
     * @param  Mixed  $data        
     * @param  String $transformer 
     * @return Mixed              
     */
    public function transform($data, $transformer=null)
    {
        $transformer = ($transformer) ? $transformer : ucfirst(debug_backtrace()[1]['function']).'Transformer';  
        return $this->resolveTransformer($transformer)->transform($data);
    }

    /**
     * Retrieves Manager Instance on fly
     * Example : customer returns CustomerManager if exists
     * @param  String $manager_name
     * @return BaseManager
     */
    public function from($manager_name)
    {
        return $this->resolveManager($manager_name);
    }

    /**
     * Resolve Validator Based on the namespace 
     * @param  String $validator_name 
     * @return CustomValidator
     */
    private function resolveValidator($validator_name)
    {
        if(isset($this->validators[$validator_name]))
        {
            return $this->validators[$validator_name];
        }
        
        $namespace = $this->resolveNamespace(). "\Validators\\".$validator_name;
        return $this->validators[$validator_name] = resolve($namespace);
    }

    /**
     * Resolve Transformer Based on the namespace 
     * @param  String $transformer_name 
     * @return BaseTransormer
     */
    private function resolveTransformer($transformer_name)
    {
        if(isset($this->transformers[$transformer_name]))
        {
            return $this->transformers[$transformer_name];
        }
        
        $namespace = $this->resolveNamespace(). "\Transformers\\".$transformer_name;
        return $this->transformers[$transformer_name] = resolve($namespace);
    }

    /**
     * Resolve Event Based on the namespace 
     * @param  String $event_name 
     * @return BaseEvent
     */
    private function resolveEvent($event_name)
    {
        if(isset($this->events[$event_name]))
        {
            return $this->events[$event_name];
        }
        
        $namespace = $this->resolveNamespace(). "\Events\\".$event_name;
        return $this->events[$event_name] = resolve($namespace);
    }

    /**
     * Resolve Task Based on the namespace 
     * @param  String $task_name 
     * @return BaseTask
     */
    private function resolveTask($task_name)
    {
        if(isset($this->tasks[$task_name]))
        {
            return $this->tasks[$task_name];
        }

        $namespace = $this->resolveNamespace(). "\Tasks\\".$task_name;
        return $this->tasks[$task_name] = resolve($namespace);
    }

    /**
     * Resolve Manager Instance Based managers anatomy 
     * @param  String $manager_name 
     * @return BaseManager
     */
    private function resolveManager($manager_name)
    {
        if(isset($this->assiocated_managers[$manager_name]))
        {
            return $this->assiocated_managers[$manager_name];
        }

        if(preg_match('/((?:\\{1,2}\w+|\w+\\{1,2})(?:\w+\\{0,2})+)/', $manager_name))
        {
            $namspeace = $manager_name;
        }
        else
        {
            $manager_name = ucfirst($manager_name);
            $namespace = "\O3L\Managers\\{$manager_name}\\{$manager_name}"."Manager";
            return $this->assiocated_managers[$manager_name] = resolve($namespace);
        }
    }

    /**
     * Resolves current working namespace
     * @return String PSR-4 Namespace
     */
    protected function resolveNamespace()
    {
        if(isset($this->namespace))
        {
            return $this->namespace;
        }

        $reflection = new \ReflectionClass($this);
        return $this->namespace = $reflection->getNamespaceName();
    }

}
