<?php
namespace zeus\domain\event;

use zeus\utils\UUIDGenerator;

abstract class DomainEvent
{
	protected $eventId;
	protected $data;
	
	protected $eventType;
	protected $method;
	
	protected $sender;
	
	public function __construct($sender,$data)
	{
		$this->eventId = UUIDGenerator::numberNo();
		
		$class = get_class($this);
		$classVal = explode("\\",$class);
		$simpleClassVal = end($classVal);
		
		$method_name = 'handler'.$simpleClassVal;
		
		$this->eventType = $class;
		$this->method = $method_name;
		$this->data = $data;
		
		$this->sender = $sender;
	}
	
	public function eventType()
	{
		return $this->eventType;
	}
	
	public function eventId()
    {
        return $this->eventId;
    }

    public function data()
    {
        return $this->data;
    }
    
    public function handler($handler){
    	if(is_object($handler) && method_exists($handler, $this->method)){
    		$handler->{$this->method}($this);
    	}
    }
    
    public function callback($data=null){
    	if(is_object($this->aggregate) && method_exists($this->aggregate, $this->method)){
    		$this->sender->{$this->method}($this,$data);
    	}
    }
}