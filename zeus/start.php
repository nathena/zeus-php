<?php
if ( strnatcasecmp(phpversion(),'5.3') <= 0 )
{
	exit(" Must use php 5.3+");
}

use zeus\loader\Autoloader;
use zeus\log\Logger;
use zeus\mvc\Router;
use zeus\exception\ControllerNotFoundException;
use zeus\mvc\Controller;
use zeus\Env;
use zeus\filter\XssFilter;

define('ZEUS_VERSION', '0.0.1');
define('ZEUS_PATH', dirname(__FILE__));
define('ZEUS_START_TIME', microtime(true));
define('ZEUS_START_MEM', memory_get_usage());
define("DS", DIRECTORY_SEPARATOR);

require_once 'loader/Autoloader.php';

Autoloader::getInstance()->register('zeus', dirname(__FILE__));

$router = new Router();

try 
{
	if(!$router->doRoute())
	{
		throw new ControllerNotFoundException($router->getOrginPath(),' not match the controller.');
	}
	
	$controller = new $router->getController();
	if( $controller instanceof Controller )
	{
		if( Env::config("xss_clean") )
		{
			$filter = new XssFilter();
			$data = $filter->doFilter($router->getParams());
			
			$router->setParams($data);
			
			$controller->__filter($filter);
		}
		
		call_user_func_array(array($controller,$router->getMethod()), $router->getParams());
	}
	else 
	{
		throw new ControllerNotFoundException($router->getOrginPath(),' not match the controller.');
	}
}
catch(Exception $e)
{
	__exception_handler($e);
}

// if( Env::config("xss_clean") )
// {
// 	$filter = new XssFilter();
	
// 	$_GET = $filter->doFilter($_GET);
// 	$_POST = $filter->doFilter($_POST);
// 	$_COOKIE = $filter->doFilter($_COOKIE);
	
	
// }

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