<?php
namespace zeus\event;

abstract class EventObject
{
	protected $eventId;
	protected $eventType;
	
	protected $sender;
	
	protected $data;
	protected $method;
	
	public function __construct($sender,$data)
	{
		$class = get_class($this);
		$classVal = explode("\\",$class);
		$simpleClassVal = end($classVal);
		
		$method_name = 'handler'.$simpleClassVal;
		
		$this->eventType = $class;
		$this->eventId = $this->eventType.time();
		$this->method = $method_name;
		$this->data = $data;
		
		$this->sender = $sender;
	}
	
	public function getEventType()
	{
		return $this->eventType;
	}
	
	public function getEventId()
    {
        return $this->eventId;
    }

    public function getData()
    {
        return $this->data;
    }
    
    public function getSender(){
    	return $this->sender;
    }
    
    public function handler($handler){
    	if(is_object($handler) && method_exists($handler, $this->method)){
    		$handler->{$this->method}($this);
    	}
    }
    
    public function callback($data=null){
    	if(is_object($this->sender) && method_exists($this->sender, $this->method)){
    		$this->sender->{$this->method}($this,$data);
    	}
    }
}