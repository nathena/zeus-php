<?php
/**
 * User: nathena
 * Date: 2017/6/12 0012
 * Time: 9:59
 */

namespace test;


use zeus\base\AbstractCommand;

class EchoCommand extends AbstractCommand
{
    public function __construct()
    {
        parent::__construct();

        $this->subscribe(EchoCommandHandler::class);

        $this->msg = "hello";
    }

    protected function start(){
        echo "{$this->commandType} => starting \r\n";
    }

    protected function finished(){
        echo "{$this->commandType} => finished \r\n";
    }
}