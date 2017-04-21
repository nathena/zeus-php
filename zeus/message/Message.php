<?php
namespace zeus\message;

class Message
{
	private static $instances = [];
	
	/**
	 * 
	 * @param string $ns
	 * @return \zeus\message\Message
	 */
	public static function getInstance($ns="default"){
		if(!isset(self::$instances[$ns])){
			self::$instances[$ns] = new self();
		}
		return self::$instances[$ns];
	}
	
	public function publish(MessageObject $msg){
		
		$method_name = $msg->getHandlerMethod();
		$subscribers = $msg->getSubscribers();
		
		foreach($subscribers as $subscriber){
			if(!empty($subscriber) && class_exists($subscriber)){
				$obj = new $subscriber();
				if(is_object($obj) && method_exists($obj, $method_name)){
					$obj->{$method_name}($msg);
				}
			}
		}
	}
}