<?php
/**
 * User: nathena
 * Date: 2017/6/12 0012
 * Time: 11:18
 */

namespace zeus\base\bus;


use zeus\base\event\EventListenerInterface;
use zeus\base\event\EventMessage;

class EventBus
{
    private static $_listeners = [];
    private static $_instance;

    /**
     * @return EventBus
     */
    public static function getInstance()
    {
        if(!isset(self::$_instance)){
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    protected function __construct()
    {
    }

    public function subscribe($eventType,$eventListner){
        if(!isset(self::$_listeners[$eventType])){
            self::$_listeners[$eventType] = [];
        }
        self::$_listeners[$eventType][] = $eventListner;
    }

    public function publish(EventMessage $eventMessage){
        $event = $eventMessage->getEvent();
        $event->start();
        $eventType = $event->getEventType();
        $_listeners = self::$_listeners[$eventType];
        foreach($_listeners as $_listener){
            if(!empty($_listener) && class_exists($_listener)){
                $_listener = new $_listener();
                if(is_object($_listener) && $_listener instanceof EventListenerInterface){
                    $_listener->handler($eventMessage);
                }
            }
        }
        $event->finished();
    }
}