<?php
/**
 * User: nathena
 * Date: 2017/6/12 0012
 * Time: 9:59
 */

namespace command;


use zeus\base\command\CommandHandlerInterface;
use zeus\base\command\CommandInterface;

class EchoCommandHandler implements CommandHandlerInterface
{
    public function execute(CommandInterface $command)
    {
        if($command instanceof EchoCommand)
        {
            echo __CLASS__,':',$command->name;
        }
    }
}