<?php
namespace zeus\mvc;

use zeus\http\Request;
use zeus\env\Env;

class Router
{
	private static $config = [
			//REQUEST_URI、QUERY_STRING、PATH_INFO
			'uri_protocol'				=> 'REQUEST_URI',
			'default_controller_ns'	    => '',
			'default_controller'		=> 'Index',
			'default_controller_sepc'	=> 'Controller',
			'default_controller_action'	=> 'index',
			'404_override'				=> true,
			//(\w) => app\controller\WelcomeController@index#$1
			'rewrite'					=> []
	];
	
	private $orgin_path;
	private $controller;
	private $method;
	private $params = [];
	private $route_not_matched = false;
	
	private function _parse_argv()
	{
		$args = array_slice($_SERVER['argv'], 1);
		return $args ? implode('/', $args) : '';
	}
	
	public function __construct($url_path = '',array $config = [])
	{
		if( is_array($url_path) )
		{
			$config = $url_path;
		}
		
		if (empty($config)) 
		{
			$config = Env::route();
		}
		
		self::$config = array_merge(self::$config,array_change_key_case($config));
		
		if( empty($url_path) )
		{
			$url_path = Request::isCli()?$this->_parse_argv():$_SERVER[self::$config['uri_protocol']];
		}
		
		$url_path = parse_url($url_path);
		
		$this->orgin_path = trim(strtolower($url_path["path"]),"/");
		if( isset($url_path["query"]))
		{
			parse_str($url_path["query"],$_GET);
		}
		$this->parse();
	}
	
	public function getOrginPath()
	{
		return $this->orgin_path;
	}
	
	public function getController()
	{
		return $this->controller;
	}
	
	public function getMethod()
	{
		return $this->method;
	}
	
	public function getParams()
	{
		return $this->params;
	}
	
	public function setParams(array $params)
	{
		$this->params = $params;
	}
	
	protected function doRoute()
	{
		$uri_path = $this->orgin_path;
		if( "/" == $uri_path || "" == $uri_path )
		{
			if( $this->routeDefauleController() )
			{
				return true;
			}
		}
		
		if( $this->routerUriRewrite() )
		{
			return true;
		}
		
		if( $this->routeUriPath() )
		{
			return true;
		}
		
		if( self::$config['404_override'] )
		{
			return $this->routeDefauleController();
		}
		
		return false;
	}
	
	protected function routerUriRewrite()
	{
		$rewrite = self::$config['rewrite'];
		$uri_path = $this->orgin_path;
		
		if( !empty($rewrite) && is_array($rewrite))
		{
			foreach ($rewrite as $pattern => $replacement )
			{
				if( preg_match("#^$pattern$#", $uri_path))
				{
					$rule = $preg_replace("#^$pattern$#", $replacement, $uri_path);
		
					$rule = explode("@", $rule);
					
					if( class_exists($rule[0]))//autoload
					{
						$this->controller = $rule[0];
						
						$rule = explode("#",$rule[1]);
						$this->method = $rule[0];
						
						if( count($rule)>1 )
						{
							$this->params = explode(",", $rule[1]);
						}
						
						return true;
					}
				}
			}
		}
		
		return false;
	}
	
	protected function routeUriPath()
	{
		$uri_path = $this->orgin_path;
		
		$rule = explode("/", $uri_path);
		$count = count($rule);
		
		$controller_packpage = self::$config['default_controller_ns'].'\\controller';
		
		$index = 0;
		do 
		{
			$controller = $controller_packpage.'\\'.ucfirst($rule[$index]).self::$config['default_controller_sepc'];
			$controller_packpage = $controller_packpage.'\\'.$rule[$index];
			
			if( class_exists($controller) )
			{
				$this->controller = $controller;
				
				if( $index+1 < $count)
				{
					$_rule = array_slice($rule, $index+1);
					$this->method     = $_rule[0];
				}
				else 
				{
					$this->method = self::$config['default_controller_action'];
				}
				
				
				if( $index+2 < $count)
				{
					$this->params     = array_slice($rule, $index+2);
				}
				
				return true;
			}
			
			$index++;
		}
		while($index<$count);
		
		return false;
	}
	
	private function routeDefauleController()
	{
		$controller = self::$config['default_controller_ns'].'\\controller\\'.self::$config['default_controller'].self::$config['default_controller_sepc'];
		if( class_exists($controller) )
		{
			$this->controller = $controller;
			$this->method     = self::$config['default_controller_action'];
		
			return true;
		}
		
		return false;
	}
}