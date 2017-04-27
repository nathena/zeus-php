<?php
namespace zeus\command;

abstract class Command
{
	protected $commandType;
	protected $commandId;
	
	protected $method;
	
	public function __construct()
	{
		$class = get_class($this);
		$classVal = explode("\\",$class);
		$simpleClassVal = end($classVal);
		
		$method_name = 'handler'.$simpleClassVal;
	
		$this->commandType = $class;
		$this->commandId = $this->commandType.time();
		
		$this->method = $method_name;
	}
	
	public function getCommandType()
	{
		return $this->commandType;
	}
	
	public function getCommandId()
	{
		return $this->commandId;
	}
	
	public function handler($handler){
		if(is_object($handler) && method_exists($handler, $this->method)){
			$handler->{$this->method}($this);
		}else{
			throw new \RuntimeException(get_class($handler).':'.$this->method);
		}
	}
	
	public function check(){
		
	}
}