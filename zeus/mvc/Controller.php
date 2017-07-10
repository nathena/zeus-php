<?php

namespace zeus\mvc;

use zeus\base\logger\Logger;
use zeus\utils\UUIDGenerator;

abstract class Controller
{
    protected $request;
    protected $response;

    public function __construct()
    {
        $this->request = Application::getInstance()->getRequest();
        $this->response = Application::getInstance()->getResponse();

        if( $this->request->getMethod() == 'POST' && !$this->check_csrf_token())
        {
            throw new \RuntimeException("Illegal requests");
        }
    }

    public function beforeAction()
    {

    }

    public function afterAction()
    {

    }

    /**
     * @param $tpl_path
     * @return View
     */
    public function getView($tpl_path)
    {
        $csrf_token = UUIDGenerator::randChar(5);

        $session = $this->request->getSession();
        $session['csrf_token'] = $csrf_token;

        $view = new View($tpl_path);
        $view['csrf_token'] = $csrf_token;

        return $view;
    }

    public function errorHandler(\Exception $e)
    {
        echo $e->getMessage(),':',$e->getTraceAsString();
    }

    protected function forward($url_path)
    {
        Application::getInstance()->dispatch($url_path);
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