<?php
/**
 * User: nathena
 * Date: 2017/6/12 0012
 * Time: 11:18
 */

namespace zeus\base\bus;


use zeus\base\command\AbstractCommand;
use zeus\base\command\CommandHandlerInterface;

class CommandBus
{
    private static $_handlers = [];
    private static $_instance;

    /**
     * @return CommandBus
     */
    public static function getInstance()
    {
        if(!isset(self::$_instance)){
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    protected function __construct()
    {
    }

    public function register($commandType,$commandHandler){
        if(!isset(self::$_handlers[$commandType])){
            self::$_handlers[$commandType] = [];
        }
        self::$_handlers[$commandType][] = $commandHandler;
    }

    public function execute(AbstractCommand $command){
        $command->start();
        $commandType = $command->getCommandType();
        $_handlers = self::$_handlers[$commandType];
        foreach($_handlers as $_handler){
            if(!empty($_handler) && class_exists($_handler)){
                $_handler = new $_handler();
                if(is_object($_handler) && $_handler instanceof CommandHandlerInterface){
                    $_handler->execute($command);
                }
            }
        }
        $command->finished();
    }
}