<?php
namespace zeus\event;

abstract class EventObject
{
	protected $eventId;
	protected $eventType;
	
	protected $sender;
	protected $method;
	

	protected $data = [];
	
	public function __construct($sender)
	{
		$class = get_class($this);
		$classVal = explode("\\",$class);
		$simpleClassVal = end($classVal);
		
		$method_name = 'handler'.$simpleClassVal;
		
		$this->eventType = $class;
		$this->eventId = $this->eventType.time();
		$this->method = $method_name;
		
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
    
    public function subscribe($eventHandler){
    	EventPublisher::getInstance()->subscribe($this->eventType, $eventHandler);
    }
    
    public function publish(){
    	EventPublisher::getInstance()->publish($this);
    }
    
    public function getData()
    {
    	return $this->data;
    }
    
    private function __get($key){
    	if(isset($this->data[$key])){
    		return $this->data[$key];
    	}
    	return null;
    }
    
    private function __set($key,$val){
    	$this->data[$key] = $val;
    }
}