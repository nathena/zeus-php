<?php
/**
 * User: nathena
 * Date: 2017/6/12 0012
 * Time: 11:18
 */

namespace zeus\base\query;


class QueryDispatcher
{
    private static $_instance;

    /**
     * @return QueryDispatcher
     */
    public static function getInstance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    protected function __construct()
    {
    }

    public function execute(CommandInterface $command)
    {
        $handlerList = $command->getHandlerList();
        foreach($handlerList as $handlerClass)
        {
            if(class_exists($handlerClass))
            {
                $handler = new $handlerClass();
                if($handler instanceof CommandHandlerInterface)
                {
                    $handler->execute($command);
                }
            }
        }
    }
}