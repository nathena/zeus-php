<?php
/**
 * User: nathena
 * Date: 2017/6/12 0012
 * Time: 9:59
 */

namespace test;


use zeus\base\AbstractComponent;

class EchoedEventHandler extends AbstractComponent
{
    public function onEchoedEvent(EchoedEvent $event)
    {
        echo get_class($this),"\r\n";
        print_r($event->getResult());
    }
}