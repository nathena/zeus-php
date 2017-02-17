<?php
namespace zeus\foundation\event;

/**
 * event engine
 * @author nathena
 *
 */
class Event
{
	public static function publish(EventObject $event)
	{
		$_handlers = self::getHandler($event);
		foreach($_handlers as $handler){
			if(class_exists($handler)){
				$callable = new $handler();
				$callable->call($event);
			}
		}
	}
	
	private static function getHandler($event){
		$handler = [];
		$_class = get_class($event);
		if(!empty($_class)){
			$_event_key = trim($_class);
			if(isset(EventManager::$events[$_event_key])){
				$handler = EventManager::$events[$_event_key];
			}
		}
		return $handler;
	}
}