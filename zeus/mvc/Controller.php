<?php

namespace zeus\mvc;

use zeus\utils\UUIDGenerator;

abstract class Controller
{
    protected $request;
    protected $response;

    /**
     * @var ModelMap
     */
    protected $modelMap;

    public function __construct()
    {
        $this->request = Application::getInstance()->getRequest();
        $this->response = Application::getInstance()->getResponse();

        $this->check_request();
    }

    public function beforeAction()
    {

    }

    public function afterAction()
    {

    }

    public function setModelMap(ModelMap $modelMap)
    {
        $csrf_token = UUIDGenerator::randChar(5);

        $session = $this->request->getSession();
        $session['csrf_token'] = $csrf_token;

        $modelMap['csrf_token'] = $csrf_token;

        $this->modelMap = $modelMap;
    }

    public function errorHandler(\Exception $e)
    {
        echo $e->getMessage(),':',$e->getTraceAsString();
    }

    protected function check_request()
    {
        if( $this->request->getMethod() == 'POST' && !$this->check_csrf_token())
        {
            throw new \RuntimeException("Illegal requests");
        }
    }

    protected function check_csrf_token()
    {
        $session = $this->request->getSession();
        $req_csrf_token = $this->request['csrf_token'];
        $session_csrf_token = $session['csrf_token'];

        unset($session['csrf_token']);

        if (empty($req_csrf_token)) {
            return false;
        }

        if ($req_csrf_token != $session_csrf_token) {
            return false;
        }

        return true;
    }
}