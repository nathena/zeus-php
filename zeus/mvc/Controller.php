<?php

namespace zeus\mvc;

use zeus\utils\UUIDGenerator;

abstract class Controller
{
    protected $request;
    protected $response;

    public function __construct()
    {
        $this->request = Application::getInstance()->getRequest();
        $this->response = Application::getInstance()->getResponse();
    }

    /**
     * @param $tpl_path
     * @return View
     */
    public function getView($tpl_path)
    {
        $csrf_token = UUIDGenerator::randChar(5);

        $session = $this->request->getSession();
        $session->csrf_token = $csrf_token;

        $view = new View($this->request, $this->response, $tpl_path);
        $view->csrf_token = $csrf_token;

        return $view;
    }

    public function errorHandler(\Exception $e)
    {
        if($this->request->isAjax()){
            $this->response->setBody($e->getMessage())->send("json");
        }else{
            $str = '<style>body {font-size:12px;}</style>';
            $str .= '<h1>操作失败！</h1><br />';
            $str .= '<strong>错误信息：<strong><font color="red">' . $e->getMessage() . '</font><br />';

            $this->response->setBody($str)->send();
        }
    }

    protected function forward($url_path)
    {
        Application::getInstance()->dispatch($url_path);
    }

    protected function check_csrf_token()
    {
        $session = $this->request->getSession();
        $req_csrf_token = $this->request->csrf_token;
        $session_csrf_token = $session->csrf_token;

        unset($session->csrf_token);

        if (empty($req_csrf_token)) {
            return false;
        }

        if ($req_csrf_token != $session_csrf_token) {
            return false;
        }

        return true;
    }
}