<?php
/**
 * User: nathena
 * Date: 2017/6/12 0012
 * Time: 9:59
 */

namespace test;


use zeus\base\event\EventListenerInterface;
use zeus\base\event\EventMessage;

class EchoedEventHandler implements EventListenerInterface
{
    public function handler(EventMessage $eventMessage)
    {
        $sender = $eventMessage->getSender();
        $event  = $eventMessage->getEvent();

        echo "sender : ".get_class($sender)."=>\r\n";
        print_r($event->getData());
    }
}