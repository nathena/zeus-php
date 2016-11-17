<?php
namespace zeus\foundation\event;

class Event
{
	protected static $_listener = [];
	
	public static function subscribe(EventObject $event,EventCallable $callable)
	{
		self::$_listener[get_class($event)] = $callable;
	}
	
	public static function publish(EventObject $event)
	{
		$_class = get_class($event);
		if( isset($_listener[$_class]) )
		{
			self::$_listener[$_class]->call($event);
		}
		
		$event->__publish();
	}
}