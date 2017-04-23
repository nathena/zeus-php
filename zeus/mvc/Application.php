<?php
namespace zeus\mvc;

use zeus\exception\NestedException;

class Application 
{
	private static $_applications = [];
	
	private $request;
	private $reponse;
	
	private $router;
	private $filter;
	private $view;
	
	protected function __construct()
	{
	}
	
	/**
	 * 
	 * @param string $ns
	 * @return \zeus\mvc\Application
	 */
	public static function getInstance($ns = 'default')
	{
		if(!isset(self::$_applications[$ns])){
			self::$_applications[$ns] = new self();
		}
		return self::$_applications[$ns];
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