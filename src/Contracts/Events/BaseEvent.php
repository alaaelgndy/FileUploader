<?php
namespace Elgndy\FileUploader\Contracts\Events;

use Illuminate\Queue\SerializesModels;
use Elgndy\FileUploader\Contracts\Events\EventInterface;

abstract class BaseEvent implements EventInterface
{
    use SerializesModels;

    protected $data = null;

    /**
     * Sets Event's holding data
     * @param mixed|array $data 
     */
    private function set($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Retrieve's Event data
     * @return mixed|array 
     */
    public function get($key = null)
    {
        if(isset($key) && $this->data[$key]){
            return $this->data[$key];
        }
        return $this->data;
    }

    /**
     * Fires Event
     * @param  array  $data 
     * @return Symfony\Component\EventDispatcher       
     */
    public function fire($data=[])
    {
        $this->set($data);
        event($this);
    }

} 
