<?php
namespace zeus\foundation\mvc;

use zeus\foundation\Autoloader;
use zeus\foundation\http\Request;
use zeus\foundation\http\Response;
use zeus\foundation\http\RequestXssFilter;
use zeus\foundation\filter\FilterInterface;
use zeus\foundation\filter\DefaultFilter;
use zeus\foundation\filter\XssFilter;
use zeus\foundation\mvc\Controller;
use zeus\foundation\mvc\Router;
use zeus\foundation\logger\Logger;
use zeus\foundation\ConfigManager;
use zeus\foundation\exception\NestedException;

define('ZEUS_VERSION', '0.0.1');
define('ZEUS_PATH', dirname(dirname(dirname(__FILE__))));
define('ZEUS_START_TIME', microtime(true));
define('ZEUS_START_MEM', memory_get_usage());
define("DS", DIRECTORY_SEPARATOR);

defined('APP_ENV_DIR') or define('APP_ENV_DIR', ZEUS_PATH);

require_once ZEUS_PATH.DS.'foundation'.DS.'Autoloader.php';

class Application 
{
	private static $_applications = [];
	
	private $autoloader;
	
	private $request;
	private $reponse;
	
	private $router;
	private $filter;
	
	private $view;
	
	protected function __construct()
	{
		$this->autoloader = new Autoloader();
		$this->autoloader->registerNamespaces('zeus', ZEUS_PATH);
		$this->autoloader->registerDirs([ZEUS_PATH,ZEUS_PATH.DS.'lib']);
		
		$this->init();
	}
	
	protected function init()
	{
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
		
		$this->setRequest(Request::create($this));
		$this->setResponse(Response::create($this));
		
		$this->setFilter(new DefaultFilter());
		$this->setRouter(new Router());
		$this->setView(new View());
	}
	
	/**
	 * 
	 * @param string $ns
	 * @return \zeus\foundation\mvc\Application
	 */
	public static function getCurrentApplication($ns = 'default')
	{
		if(!isset(self::$_applications[$ns])){
			self::$_applications[$ns] = new self();
		}
		return self::$_applications[$ns];
	}
	
	public function currentAutoLoad()
	{
		return $this->autoloader;
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
	
	public function getView()
	{
		return $this->view;
	}
	
	public function setView(View $view)
	{
		$this->view = $view;
	}
	
	public function start($orgin_path='')
	{
		$controller = null;
		try
		{
			if( empty($orgin_path) ){
				$orgin_path = $this->request->getOrginPath(ConfigManager::config("uri_protocol"));
			}
			
			$this->router->route($orgin_path);
			
			if( ConfigManager::config("xss_clean") )
			{
				$this->filter->setNext(new XssFilter());
			}
			
			$this->request->params($this->router->getParams());
			$this->request->doFilter($this->filter);
			
			$controller = $this->router->getController();
			$controller = new $controller();
			if( $controller instanceof Controller )
			{
				call_user_func_array(array($controller, $this->router->getMethod()),$this->request->params());
			}
		}
		catch(NestedException $e)
		{
			if( !is_null($controller) && $controller instanceof Controller )
			{
				$controller->errorHandler($e);
			}
			else 
			{
				throw $e;
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