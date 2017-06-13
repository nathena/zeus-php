<?php
/**
 * User: nathena
 * Date: 2017/6/12 0012
 * Time: 9:59
 */

namespace test;


use zeus\base\AbstractComponent;
use zeus\base\command\AbstractCommand;
use zeus\base\command\CommandHandlerInterface;

class EchoCommandHandler extends AbstractComponent implements CommandHandlerInterface
{
    public function execute(AbstractCommand $command)
    {
        print_r($command->getData());

        $this->raise(new EchoedEvent());
    }
}