<?php
namespace zeus\message;

abstract class MessageObject
{
	protected $subscribers = [];
	
	private $sender;
	
	private $class;
	private $handlerMethod;
	private $callbackMethod;
	
	public function __construct($sender){
		$this->sender = $sender;
		
		$class = get_class($this);
		$classVal = explode("\\",$class);
		$simpleClassVal = end($classVal);
		
		$method_name = 'handler'.$simpleClassVal;
		$callback_name = 'callback'.$simpleClassVal;
		
		$this->class = $class;
		$this->handlerMethod = $method_name;
		$this->callbackMethod = $callback_name;
	}
	
	public function getSender()
	{
		return $this->sender;
	}
	
	public function getSubscribers()
	{
		return $this->subscribers;
	}
	
	public function getHandlerMethod(){
		return $this->handlerMethod;
	}
	
	public function callback(){
		if(method_exists($this->sender, $this->callbackMethod)){
			$this->sender->{$this->callbackMethod}($this);
		}
	}
}