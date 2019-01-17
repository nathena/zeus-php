<?php
/**
 * User: nathena
 * Date: 2017/6/12 0012
 * Time: 9:59
 */

namespace event;


use zeus\base\event\EventInterface;
use zeus\base\event\EventListenerInterface;

class EchoedEventHandler implements EventListenerInterface
{
    public function handler(EventInterface $event)
    {
        if($event instanceof EchoedEvent)
        {
            echo __CLASS__,":",$event->name;
        }
    }
}