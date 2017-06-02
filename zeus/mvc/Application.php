<?php
namespace zeus\mvc;

use zeus\http\Response;
use zeus\http\XssWapperRequest;

class Application 
{
    private static $instance;

	private $request;
	private $response;

	/**
     * @return \zeus\mvc\Application
     */
	public static function getInstance(){
        if(!isset(static::$instance)){
            static::$instance = new static();
        }
        return static::$instance;
    }

	public function forward($url_path)
	{
		$controller = null;
		try
		{
		    $router = new Router($url_path);

			$controllerClass = $router->getController();
			$controller = new $controllerClass();
			if( !($controller instanceof Controller) )
			{
                throw new ControllerNotFoundException("{$controllerClass} 控制器不是系统控制器子类");
			}
            call_user_func_array(array($controller, $router->getAction()),$router->getParams());
		}
		catch(\Exception $e)
		{
			if( is_null($controller) || ! ($controller instanceof Controller) )
			{
                throw $e;
			}
            $controller->errorHandler($e);
		}
	}

	public function getRequest(){
	    return $this->request;
    }

    public function getResponse(){
	    return $this->response;
    }


    protected function __construct($url_path)
    {
        $this->request = new XssWapperRequest();
        $this->response = new Response();
    }

}