<?php
namespace zeus\mvc;

use zeus\util\UuidHelper;
use zeus\http\Request;
use zeus\http\Response;
use zeus\Application;

class Controller
{
	protected $view;
	
	protected $request;
	protected $reponse;
	
	protected $application;
	
	public function __construct()
	{
		
	}
	
	public function setApplication(Application $application)
	{
		$this->application = $application;
		
		$this->view = new View();
		
		$this->setRequest($application->getRequest());
		$this->setResponse($application->getReponse());
	}
	
	public function getApplication()
	{
		return $this->application;
	}
	
	public function crsfToken()
	{
		$uuid = new UuidHelper();
		$token = $uuid->randChar(5);
		
		Session::set("_token",$token);
		
		return $token;
	}
	
	public function checkCrsfToken($token)
	{
		$_token = session::get("_token");
		return $token == $_token;
	}
	
	public function __errorHandler($message, $code='', $type='', $file="", $line="")
	{
		$this->view->assign("message",$message);
		$this->view->assign("code",$code);
		$this->view->assign("type",$type);
		$this->view->assign("file",$file);
		$this->view->assign("line",$line);
		
		$this->view->display("error");
	}
	
	protected function getRequest()
	{
		return $this->request;
	}
	
	protected function setRequest(Request $request)
	{
		$this->request = $request;
	}
	
	protected function getResponse()
	{
		return $this->response;
	}
	
	protected function setResponse(Response $response)
	{
		$this->response = $response;
	}
}