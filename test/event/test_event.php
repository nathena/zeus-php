<?php
/**
 * User: nathena
 * Date: 2017/9/22 0022
 * Time: 10:37
 */

include_once __DIR__.'/../bootstrap.php';
\zeus\sandbox\Autoloader::getInstance()->registerNamespaces("event",__DIR__);

\zeus\base\event\EventDispatcher::getInstance()->publish(new \event\EchoedEvent());