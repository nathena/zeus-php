<?php
/**
 * Created by IntelliJ IDEA.
 * User: nathena
 * Date: 2019/1/17 0017
 * Time: 10:51
 */

namespace event;

use zeus\base\event\EventInterface;

class EchoedEvent implements EventInterface
{
    public $name = "EchoedEvent";

    public function getListenerList()
    {
        return [
            'event\EchoedEventHandler',
        ];
    }
}