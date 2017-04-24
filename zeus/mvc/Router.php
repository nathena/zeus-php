<?php
namespace zeus\mvc;

use zeus\sandbox\ConfigManager;
use zeus\http\Request;

class Router
{
	private static $inited = false;
	private static $config = [
			'default_controller_ns'	    => '',
			'default_controller'		=> 'Index',
			'default_controller_action'	=> 'index',
			'404_override'				=> true,
			//(\w) => app\controller\Index@index#$1
			'rewrite'					=> []
	];
	
	protected $controller;
	protected $method;
	protected $params = [];
	
	protected $uri_path;
	
	public function __construct(Request $request)
	{
		$this->init();
		$this->uri_path = trim($request->getOrginPath(),"/");
		$this->params = array_merge($this->params,$request->getData());
		$this->route();
	}
	
	private function init(){
		if(static::$inited){
			return;
		}
		$config = ConfigManager::router();
		$config = array_merge(static::$config,array_change_key_case($config));
		
		static::$config = $config;
		static::$inited = true;
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
	
	private function route()
	{
		if( "/" == $this->uri_path || "" == $this->uri_path )
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
	
	private function routerUriRewrite()
	{
		$rewrite = self::$config['rewrite'];
		
		if( !empty($rewrite) && is_array($rewrite))
		{
			foreach ($rewrite as $pattern => $replacement )
			{
				if( preg_match("#^$pattern$#", $this->uri_path))
				{
					$rule = preg_replace("#^$pattern$#", $replacement, $this->uri_path);
		
					$rule = explode("@", $rule);
					
					if( class_exists($rule[0]))//autoload
					{
						$this->controller = $rule[0];
						
						$rule = explode("#",$rule[1]);
						$this->method = $rule[0];
						
						if( count($rule)>1 )
						{
							$this->params(explode(",", $rule[1]));
						}
						
						return true;
					}
				}
			}
		}
		
		return false;
	}
	
	private function routeUriPath()
	{
		$rule = explode("/", $this->uri_path);
		$count = count($rule);
		
		$controller_packpage = self::$config['default_controller_ns'].'\\controller';
		
		$index = 0;
		do 
		{
			$controller = $controller_packpage.'\\'.ucfirst($rule[$index]);
			$controller_packpage = $controller_packpage.'\\'.$rule[$index];
			
			if( class_exists($controller) )
			{
				$this->controller = $controller;
				
				if( $index+1 < $count)
				{
					$_rule = array_slice($rule, $index+1);
					
					$method = $_rule[0];
					$_params_index = $index+2;
					if( method_exists($controller, $method))
					{
						$this->method     = $_rule[0];
					}
					else 
					{
						$_params_index = $index+1;
						$this->method = self::$config['default_controller_action'];
					}
					
					if( $_params_index < $count)
					{
						$this->params( array_slice($rule, $_params_index) );
					}
				}
				else 
				{
					$this->method = self::$config['default_controller_action'];
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
		$controller = self::$config['default_controller_ns'].'\\controller\\'.self::$config['default_controller'];
		if( class_exists($controller) )
		{
			$this->controller = $controller;
			$this->method     = self::$config['default_controller_action'];
			$this->params( explode("/", $this->uri_path));
			return true;
		}
		
		return false;
	}
	
	private function params(array $params)
	{
		$this->params = array_merge($this->params,$params);
	}
}