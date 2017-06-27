<?php
namespace zeus\mvc;

use zeus\mvc\exception\RoutingRepeatedException;
use zeus\sandbox\ConfigManager;
use zeus\utils\XssCleaner;

class Router
{
    private static $all_routers = [];

	protected $controller;
	protected $action;
	protected $params = [];

    private $uri_path;

	public static function addRouter($router,$handler)
    {
        $router = trim(strtolower($router),"/");
        if(isset(self::$all_routers[$router])){
            throw new RoutingRepeatedException();
        }

        self::$all_routers[$router] = $handler;
    }

    public static function getAllRouter()
    {
        return self::$all_routers;
    }

	public function __construct($uri_path)
	{
		$this->uri_path = trim($uri_path,"/");
		$this->route();
	}
	
	public function getController()
	{
		return $this->controller;
	}
	
	public function getAction()
	{
		return $this->action;
	}
	
	public function getParams()
	{
		return $this->params;
	}

    private function route()
    {
        if( $this->routerUriRewrite() )
        {
            return;
        }

//        if( $this->routeUriPath() )
//        {
//            return;
//        }

        if( $this->routeDefaultController())
        {
            return;
        }

        throw new \RuntimeException(" request not found");
    }

    private function routerUriRewrite()
    {
        $rewrite = self::$all_routers;
        if( !empty($rewrite) && is_array($rewrite) && !in_array($this->uri_path,["","/"]))
        {
            foreach ($rewrite as $pattern => $replacement )
            {
                $pattern = trim($pattern,"/");
                if( preg_match("#^$pattern$#", $this->uri_path))
                {
                    $rule = preg_replace("#^$pattern$#", $replacement, $this->uri_path);
                    $rule = explode("@", $rule);

                    if( class_exists($rule[0]))//autoload
                    {
                        $this->controller = $rule[0];
                        if(count($rule)>1){
                            $rule = explode("#",$rule[1]);
                            $this->action = $rule[0];
                            if( count($rule)>1 )
                            {
                                $this->merge_params(explode(",", $rule[1]));
                            }
                        }else{
                            $this->action = ConfigManager::config("router.default_controller_action");
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

        $controller_packpage = ConfigManager::config('router.default_controller_ns').'\\controller';

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
                        $this->action = $_rule[0];
                    }
                    else
                    {
                        $_params_index = $index+1;
                        $this->action = ConfigManager::config('router.default_controller_action');
                    }

                    if( $_params_index < $count)
                    {
                        $this->merge_params( array_slice($rule, $_params_index) );
                    }
                }
                else
                {
                    $this->action = ConfigManager::config('router.default_controller_action');
                }

                return true;
            }

            $index++;
        }
        while($index<$count);

        return false;
    }

    private function routeDefaultController()
    {
        $controller = ConfigManager::config('router.default_controller');
        if( class_exists($controller) )
        {
            $this->controller = $controller;
            $this->action     = ConfigManager::config('router.default_controller_action');
            $this->merge_params( explode("/", $this->uri_path));
            return true;
        }

        return false;
    }

    private function merge_params(array $params)
    {
        $params = XssCleaner::doClean($params);
        $this->params = array_merge($this->params,$params);
    }
}