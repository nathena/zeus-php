<?php
/**
 * User: nathena
 * Date: 2017/6/12 0012
 * Time: 9:59
 */

namespace command;


use zeus\base\command\CommandInterface;

class EchoCommand implements CommandInterface
{
    public $name = 'EchoCommand';

    public function getHandlerList()
    {
        return [
            "command\EchoCommandHandler",
        ];
    }
}
