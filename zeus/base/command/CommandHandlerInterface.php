<?php
/**
 * User: nathena
 * Date: 2017/6/12 0012
 * Time: 14:03
 */

namespace zeus\base\command;


interface CommandHandlerInterface
{
    public function execute(AbstractCommand $command);
}