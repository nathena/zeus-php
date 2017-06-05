<?php
namespace zeus\sandbox;

define('ZEUS_VERSION', '0.0.1');
define('ZEUS_PATH', dirname(__FILE__));
define('ZEUS_START_TIME', microtime(true));
define('ZEUS_START_MEM', memory_get_usage());
define("DS", DIRECTORY_SEPARATOR);

require_once ZEUS_PATH.DS.'base'.DS.'ApplicationContext.php';

ApplicationContext::currentContext()->start();