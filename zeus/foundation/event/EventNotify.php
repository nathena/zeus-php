<?php
namespace bundle\event;

/**
 * event engine
 * @author nathena
 *
 */
class EventNotify
{
	public static function publishEvent(EventObject $event)
	{
		$_handlers = $event->getListeners();
		foreach($_handlers as $handler){
			if(!empty($handler) && class_exists($handler)){
				$method_name = 'handler'.end(explode("\\",get_class($event)));
				$callable = new $handler();
				if(is_object($callable) && method_exists($callable, $method_name)){
					$callable->{$method_name}($event);
				}
			}
		}
	}
}