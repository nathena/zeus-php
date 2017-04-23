<?php
namespace zeus\command;

abstract class Command
{
	protected $type;
	protected $id;
	
	protected $method;
	
	public function __construct()
	{
		$class = get_class($this);
		$classVal = explode("\\",$class);
		$simpleClassVal = end($classVal);
		
		$method_name = 'handler'.$simpleClassVal;
	
		$this->type = $class;
		$this->id = $this->type.time();
		
		$this->method = $method_name;
	}
	
	public function getType()
	{
		return $this->type;
	}
	
	public function getId()
	{
		return $this->id;
	}
	
	public function handler($handler){
		if(is_object($handler) && method_exists($handler, $this->method)){
			$handler->{$this->method}($this);
		}
	}
}