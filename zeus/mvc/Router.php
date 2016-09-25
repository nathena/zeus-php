<?php
namespace zeus\mvc;

use zeus\http\Request;
use zeus\env\Env;
use zeus\filter\FilterInterface;
use zeus\exception\RouterNotFoundException;
use zeus\http\Response;
use zeus\filter\XssFilter;

class Router
{
	private static $config = [
			//REQUEST_URI、QUERY_STRING、PATH_INFO
			'uri_protocol'				=> 'REQUEST_URI',
			'default_controller_ns'	    => '',
			'default_controller'		=> 'Index',
			'default_controller_action'	=> 'index',
			'404_override'				=> true,
			//(\w) => app\controller\Index@index#$1
			'rewrite'					=> []
	];
	
	protected $request;
	protected $filter;
	
	protected $orgin_path;
	protected $controller;
	protected $method;
	protected $params = [];
	
	public function __construct(Request $request, FilterInterface $filter, array $config = [])
	{
		if (empty($config)) 
		{
			$config = Env::router();
		}
		
		self::$config = array_merge(self::$config,array_change_key_case($config));
		
		$this->request = $request;
		$this->filter  = $filter;
		
		$this->orgin_path = $this->getOrginPath();
	}
	
	public function getOrginPath()
	{
		return $this->request->getOrginPath(self::$config['uri_protocol']);
	}
	
	public function setOrginPath($orgin_path)
	{
		$this->request->setOrginPath($orgin_path);
		
		$this->orgin_path = $this->getOrginPath();
	}
	
	public function getRequest()
	{
		return $this->request;
	}
	
	public function getFilter()
	{
		return $this->filter;
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
	
	public function dispatch()
	{
		if( $this->doRoute() )
		{
			$controller = new $this->controller();
			if( $controller instanceof Controller )
			{
				$controller->setRouter($this);
			}
			
			if( Env::config("xss_clean") )
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
				
				$this->params = $this->filter->doFilter($this->params);
			}
			
			// echo $this->orgin_path.'<br/>';
			// echo $this->controller.'<br/>';
			// echo $this->method.'<br/>';
			// print_r($this->params).'<br/>';
			// print_r(get_included_files());
			
			call_user_func_array(array($controller, $this->method), $this->params);
			
			return false;
		}
		throw new RouterNotFoundException($this->orgin_path.'路由不存在，且不自动存在默认处理');
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
					$rule = preg_replace("#^$pattern$#", $replacement, $uri_path);
		
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
						$this->params     = array_slice($rule, $_params_index);
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
		
			return true;
		}
		
		return false;
	}
}