<?php
namespace test_command;
/**
 * Created by IntelliJ IDEA.
 * User: nathena
 * Date: 2017/6/12
 * Time: 20:00
 */
use command\EchoCommand;
use zeus\base\command\CommandDispatcher;
use zeus\sandbox\Autoloader;

include_once __DIR__.'/../bootstrap.php';
Autoloader::getInstance()->registerNamespaces("command",__DIR__);

CommandDispatcher::getInstance()->execute(new EchoCommand());


