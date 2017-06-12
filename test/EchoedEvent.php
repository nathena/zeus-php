<?php
namespace test;

use zeus\base\event\AbstractEvent;
use zeus\sandbox\ApplicationContext;

/**
 * User: nathena
 * Date: 2017/6/12 0012
 * Time: 10:04
 */
class EchoedEvent extends AbstractEvent
{
    public function __construct()
    {
        parent::__construct();

        $this->setData(["a","b","c"]);
    }

    public function start(){
        echo "{$this->eventType} => starting \r\n";
    }

    public function finished(){
        echo "{$this->eventType} => finished \r\n";
    }
}

ApplicationContext::currentContext()->getEventBus()->subscribe(EchoedEvent::class,EchoedEventHandler::class);