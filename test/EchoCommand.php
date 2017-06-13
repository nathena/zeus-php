<?php
/**
 * User: nathena
 * Date: 2017/6/12 0012
 * Time: 9:59
 */

namespace test;

use zeus\base\command\AbstractCommand;
use zeus\sandbox\ApplicationContext;

class EchoCommand extends AbstractCommand
{
    public function __construct()
    {
        parent::__construct();

        $this->setData([1,2,3]);
    }

    public function start(){
        echo "{$this->commandType} => starting \r\n";
    }

    public function finished(){
        echo "{$this->commandType} => finished \r\n";
    }

}

ApplicationContext::currentContext()->getCommandBus()->register(EchoCommand::class,EchoCommandHandler::class);