<?php
/**
 * User: nathena
 * Date: 2017/6/12 0012
 * Time: 9:59
 */

namespace test;


use zeus\base\AbstractComponent;

class EchoCommandHandler extends AbstractComponent
{
    public function handlerEchoCommand(EchoCommand $command)
    {
        $data = $command->getData();
        print_r($data);

        $this->publishMessage(new EchoedEvent($data));
    }

    public function onEchoedEvent(EchoedEvent $event)
    {
        echo get_class($this),"\r\n";
        print_r($event->getResult());
    }
}