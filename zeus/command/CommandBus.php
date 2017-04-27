<?php
namespace zeus\command;

abstract class CommandBus
{
	private static $instances = [];
	
	/**
	 *
	 * @param string $ns
	 * @return \zeus\command\CommandBus
	 */
	final public static function getInstance($ns="default"){
		if(!isset(self::$instances[$ns])){
			self::$instances[$ns] = new static();
		}
		return self::$instances[$ns];
	}
	
	private $handlers = [];
	
	protected function __construct(){
	
	}
	
	public function register($type,$handlerType){
		if(!isset($this->handlers[$type])){
			$this->handlers[$type] = [];
		}
		$this->handlers[$type][] = $handlerType;
	}
	
	public function execute(Command $command){
		$type = $command->getType();
		$handlers = $this->handlers[$type];
		foreach($handlers as $handler){
			if(!empty($handler) && class_exists($handler)){
				$command->handler(new $handler());
			}
		}
	}
}