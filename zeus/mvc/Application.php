<?php
namespace zeus\mvc;

use zeus\exception\NestedException;
use zeus\http\filter\DefaultFilter;
use zeus\http\filter\XssFilter;
use zeus\http\Request;
use zeus\http\Response;
use zeus\sandbox\ConfigManager;

class Application 
{
	private static $_applications = [];
	
	private $request;
	private $reponse;
	
	private $filter;
	
	protected function __construct()
	{
	    $this->filter = new DefaultFilter();
		if( ConfigManager::config("xss_clean") )
		{
			$this->filter->setNext(new XssFilter());
		}
		$uri_protocol = ConfigManager::config("uri_protocol");
		$uri_protocol = empty($uri_protocol)? 'REQUEST_URI':$uri_protocol;
		
		$this->request = new Request($uri_protocol);
		$this->reponse = new Response();
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
	
	public function getRequest()
	{
		return $this->request;
	}
	
	public function getResponse(){
		return $this->reponse;
	}
	
	public function dispatch()
	{
		$controller = null;
		try
		{
			$router = new Router($this->request);
			$data = $this->filter->doFilter($router->getParams());
			$this->request->setData($data);
			
			$controller = $router->getController();
			$controller = new $controller();
			if( $controller instanceof Controller )
			{
				call_user_func_array(array($controller, $router->getMethod()),$data);
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
	}
	
	public function forward($url){
		$this->request->setOrginPath($url);
		$this->dispatch();
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