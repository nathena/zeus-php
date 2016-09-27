<?php
if ( strnatcasecmp(phpversion(),'5.3') <= 0 )
{
	exit(" Must use php 5.3+");
}

use zeus\loader\Autoloader;
use zeus\log\Logger;
use zeus\http\Request;
use zeus\filter\DefaultFilter;
use zeus\env\Env;
use zeus\mvc\Router;

define('ZEUS_VERSION', '0.0.1');
define('ZEUS_PATH', dirname(__FILE__));
define('ZEUS_START_TIME', microtime(true));
define('ZEUS_START_MEM', memory_get_usage());
define("DS", DIRECTORY_SEPARATOR);

defined('APP_ENV_DIR') or define('APP_ENV_DIR', ZEUS_PATH);

require_once 'loader/Autoloader.php';

Autoloader::getInstance()->registerNamespaces('zeus', ZEUS_PATH);
Autoloader::getInstance()->registerDirs([ZEUS_PATH,ZEUS_PATH.DS.'library']);

date_default_timezone_set(empty(Env::config('time_zone')) ? 'Asia/Shanghai' : Env::config('time_zone'));

$appNamespaces = Env::config('app_ns');
foreach( $appNamespaces as $ns => $path )
{
	if( is_dir($path) )
	{
		Autoloader::getInstance()->register($ns, $path);
	}
}

try 
{
	$router = new Router(new Request(), new DefaultFilter());
	$router->dispatch();
}
catch(Exception $e)
{
	__exception_handler($e);
}

//set_exception_handler&set_error_handler
function __exception_handler($exception, $message = NULL, $file = NULL, $line = NULL)
{
	$PHP_ERROR = (func_num_args() === 5);

	if($PHP_ERROR AND (error_reporting() & $exception) === 0)
		return;

	if ($PHP_ERROR)
	{
		$code     = $exception;
		$type     = 'PHP Error';

		$message  = $type.'  '.$message.'  '.$file.'  '.$line;
	}
	else
	{
		$code     = $exception->getCode();
		$type     = get_class($exception);
		$message  = $exception->getMessage()."\n".$exception->getTraceAsString();
		$file     = $exception->getFile();
		$line     = $exception->getLine();
	}

	Logger::error($type, $code, $message, $file, $line);

	//应用没有处理错误
	$str = '<style>body {font-size:12px;}</style>';
	$str .= '<h1>操作失败！</h1><br />';
	$str .= '<strong>错误信息：<strong><font color="red">' . $message . '</font><br />';

	echo $str;
	exit($code);
}

//异常处理
set_error_handler('__exception_handler');
set_exception_handler('__exception_handler');