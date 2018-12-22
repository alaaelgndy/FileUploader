<?php
namespace Elgndy\FileUploader\Contracts\Exceptions;

use Exception;

class BaseException extends Exception
{
    /**
     * Error Messages Holder
     * @var array
     */
    private $messagesBage;

    public function __construct(
        $message, 
        array $messages = [], 
        $code=0, 
        Exception $pervious= null
    )
    {
        parent::__construct($message);
        $this->setMessages($messages);
    }

    /**
     * Set Message Bag
     * @param Array $messages 
     */
    private function setMessages(array $messages){
        $this->messagesBage = $messages;
    }

    /**
     * Retrieve Messages Bage
     * @return array
     */
    public function getMessages(){
        return $this->messagesBage;
    }
}
