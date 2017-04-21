<?php
namespace zeus\domain\event;

use zeus\utils\UUIDGenerator;

abstract class DomainEvent
{
	private $eventId;
	private $data;
	
	private $eventType;
	private $method;
	
	private $aggregate;
	
	public function __construct($data)
	{
		$this->eventId = UUIDGenerator::numberNo();
		
		$class = get_class($this);
		$classVal = explode("\\",$class);
		$simpleClassVal = end($classVal);
		
		$method_name = 'handler'.$simpleClassVal;
		
		$this->eventType = $class;
		$this->method = $method_name;
		$this->data = $data;
	}
	
	public function eventType()
	{
		return $this->eventId;
	}
	
	public function eventId()
    {
        return $this->eventId;
    }

    public function data()
    {
        return $this->data;
    }
    
    public function setAggregate($aggregate){
    	$this->aggregate = $aggregate;
    }
    
    public function handler($handler){
    	if(is_object($handler) && method_exists($handler, $this->method)){
    		$handler->{$this->method}($this);
    	}
    }
    
    public function callback($data=null){
    	if(is_object($this->aggregate) && method_exists($this->aggregate, $this->method)){
    		$this->aggregate->{$this->method}($this,$data);
    	}
    }
}