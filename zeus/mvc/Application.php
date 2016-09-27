<?php
namespace zeus;

use zeus\loader\Autoloader;
use zeus\logger\Logger;
use zeus\http\Request;
use zeus\http\Response;
use zeus\mvc\Router;
use zeus\filter\FilterInterface;
use zeus\filter\DefaultFilter;
use zeus\mvc\Controller;
use zeus\filter\XssFilter;
use zeus\etc\ConfigManager;

define('ZEUS_VERSION', '0.0.1');
define('ZEUS_PATH', dirname(dirname(__FILE__)));
define('ZEUS_START_TIME', microtime(true));
define('ZEUS_START_MEM', memory_get_usage());
define("DS", DIRECTORY_SEPARATOR);

defined('APP_ENV_DIR') or define('APP_ENV_DIR', ZEUS_PATH);

require_once ZEUS_PATH.DS.'loader/Autoloader.php';

class Application 
{
	private $request;
	private $reponse;
	
	private $router;
	private $filter;
	
	public function __construct()
	{
		$this->init();
	}
	
	public function init()
	{
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
				$autoloader->registerNamespaces($ns, $path);
			}
		}
		
		$this->setRequest(new Request());
		$this->setResponse(Response::create($this));
		
		$this->setFilter(new DefaultFilter());
		$this->setRouter(new Router($this));
	}
	
	public function getRequest()
	{
		return $this->request;
	}
	
	public function setRequest(Request $request)
	{
		$this->request = $request;
	}
	
	public function getReponse()
	{
		return $this->reponse;
	}
	
	public function setResponse(Response $response)
	{
		$this->reponse = $response;
	}
	
	public function getFilter()
	{
		return $this->filter;
	}
	
	public function setFilter(FilterInterface $filter)
	{
		$this->filter = $filter;
	}
	
	public function getRouter()
	{
		return $this->router;
	}
	
	public function setRouter(Router $router)
	{
		$this->router = $router;
	}
	
	public function run($orgin_path='')
	{
		try
		{
			$this->router->doRouter($orgin_path);
			
			if( ConfigManager::config("xss_clean") )
			{
				$_xssFilter = new XssFilter();
				$this->filter->setNext($_xssFilter);
			
				//$_GET = $this->filter->doFilter($_GET);
				//$_POST = $this->filter->doFilter($_POST);
			
				$this->request->get( $this->filter->doFilter($this->request->get()) );
				$this->request->post( $this->filter->doFilter($this->request->post()) );
				$this->request->put( $this->filter->doFilter($this->request->put()) );
				$this->request->patch( $this->filter->doFilter($this->request->patch()) );
				$this->request->delete( $this->filter->doFilter($this->request->delete()) );
				$this->request->cookie( $this->filter->doFilter($this->request->cookie()) );
				
				$this->router->setParams($this->filter->doFilter($this->router->getParams()));
			}
			
			$controller = $this->router->getController();
			$controller = new $controller();
			if( $controller instanceof Controller )
			{
				$controller->setApplication($this);
				
				call_user_func_array(array($controller, $this->router->getMethod()),$this->router->getParams());
			}
		}
		catch(\Exception $e)
		{
			__exception_handler($e);
		}
	}
	
	public function debug()
	{
		echo '<hr>';
		echo '<br>',microtime(true)-ZEUS_START_TIME;
		echo '<br>',memory_get_usage()-ZEUS_START_MEM;
		echo '<br>';
		print_r(get_included_files());
	}
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
set_error_handler(__NAMESPACE__.'\__exception_handler');
set_exception_handler(__NAMESPACE__.'\__exception_handler');