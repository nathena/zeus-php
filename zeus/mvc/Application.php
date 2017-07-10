<?php

namespace zeus\mvc;

use zeus\http\Response;
use zeus\http\XssWapperRequest;
use zeus\mvc\exception\ControllerNotFoundException;

class Application
{
    private static $instance;

    private $request;
    private $response;

    /**
     * @return \zeus\mvc\Application
     */
    public static function getInstance()
    {
        if (!isset(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    public function dispatch($url_path)
    {
        $url_path_data = parse_url($url_path);
        $path = isset($url_path_data["path"]) ? $url_path_data['path'] : "/";
        if (isset($url_path_data['query'])) {
            parse_str($url_path_data['query'], $data);
            $this->request->setData($data);
        }

        $modelMap = new ModelMap();
        $error = false;
        $controller = null;
        $result = null;
        try {
            $router = new Router($path);
            $controllerClass = $router->getController();

            $controller = new $controllerClass();
            if (!($controller instanceof Controller)) {
                throw new ControllerNotFoundException("{$controllerClass} 控制器不是系统控制器子类");
            }
            $controller->setModelMap($modelMap);

            if ($controller instanceof NeedCheckedInterface) {
                if (!$controller->do_check()) {
                    return;
                }
            }

            call_user_func_array(array($controller, "beforeAction"), [$router->getAction()]);
            $result = call_user_func_array(array($controller, $router->getAction()), $router->getParams());
            call_user_func_array(array($controller, "afterAction"), [$router->getAction()]);

        } catch (\Exception $e) {
            //ob_clean();
            $error = true;
            if (is_null($controller) || !($controller instanceof Controller)) {
                throw $e;
            }
            $result = $controller->errorHandler($e);
        }


        //handle result
        if(!empty($result)){
            $needle = 'redirect:';
            $len = strlen($needle);
            if( substr_compare($result, $needle,0, $len) === 0){
                $redirect = substr($result,$len);
                if(!empty($redirect)){
                    Response::redirect(substr($result,$len));
                }
                return;
            }

            $needle = 'forward:';
            $len = strlen($needle);
            if( substr_compare($result, $needle,0, $len) === 0){
                $forward = substr($result,$len);
                if(!empty($forward)){
                    $this->dispatch(substr($result,$len));
                }
                return;
            }


            $needle = 'json:';
            $len = strlen($needle);
            if( substr_compare($result, $needle,0, $len) === 0){
                $this->response->sendJson($modelMap->getData(),$error ? -1 : 1);
                return;
            }

            $needle = 'xml:';
            $len = strlen($needle);
            if( substr_compare($result, $needle,0, $len) === 0){
                $this->response->sendXml($modelMap->getData(),$error ? -1 : 1);
                return;
            }

            $view = new View($result,$modelMap->getData());
            $this->response->send($view->fetch());
        }
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getResponse()
    {
        return $this->response;
    }

    protected function __construct()
    {
        ob_start();

        $this->request = new XssWapperRequest();
        $this->response = new Response();
    }

    public function __destruct()
    {
        ob_end_flush();
    }
}