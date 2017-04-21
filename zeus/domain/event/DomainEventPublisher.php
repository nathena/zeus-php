<?php
namespace zeus\domain\event;

abstract class DomainEventPublisher
{
	private static $instances = [];
	
	/**
	 * 
	 * @param string $ns
	 * @return \zeus\domain\event\DomainEvent
	 */
	final public static function getInstance($ns="default"){
		if(!isset(self::$instances[$ns])){
			self::$instances[$ns] = new static();
		}
		return self::$instances[$ns];
	}
	
	private $eventHandlers = [];
	
	protected function __construct(){
		
	}
	
	public function subscribe($eventType,$handler){
		if(!isset($this->eventHandlers[$eventType])){
			$this->eventHandlers[$eventType] = [];
		}
		$this->eventHandlers[$eventType][] = $handler;
	}
	
	public function publish(DomainEvent $event){
		
		$eventType = $event->eventType();
		$eventHandlers = $this->eventHandlers[$eventType];
		
		foreach($eventHandlers as $handler){
			$event->handler($handler);
		}
		
		$this->eventHandlers[$eventType] = [];
	}
	
	public function publishAll(array $events){
		foreach ($events as $event){
			$this->publish($event);
		}
	}
}