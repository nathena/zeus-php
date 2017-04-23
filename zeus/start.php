<?php
namespace zeus\sandbox;

use zeus\mvc\Application;

define('ZEUS_VERSION', '0.0.1');
define('ZEUS_PATH', dirname(__FILE__));
define('ZEUS_START_TIME', microtime(true));
define('ZEUS_START_MEM', memory_get_usage());
define("DS", DIRECTORY_SEPARATOR);

defined('APP_ENV_DIR') or define('APP_ENV_DIR', ZEUS_PATH);

require_once 'Autoloader.php';

$autoloader = new Autoloader();
$autoloader->registerNamespaces('zeus', ZEUS_PATH);
$autoloader->registerDirs([ZEUS_PATH,ZEUS_PATH.DS.'lib']);

//timezone
date_default_timezone_set(empty(ConfigManager::config('time_zone')) ? 'Asia/Shanghai' : ConfigManager::config('time_zone'));

$appNamespaces = ConfigManager::config('app_ns');
foreach( $appNamespaces as $ns => $path )
{
	if( is_dir($path) )
	{
		$this->autoloader->registerNamespaces($ns, $path);
	}
}

$application = Application::getInstance();
$application->start();