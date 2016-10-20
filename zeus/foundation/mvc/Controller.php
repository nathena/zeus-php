<?php
namespace zeus\foundation\mvc;

use zeus\foundation\http\Request;
use zeus\foundation\http\Response;
use zeus\foundation\util\UUIDGenerator;

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
	
	public function generatCsrf()
	{
		$csrf = UUIDGenerator::randChar(5);
		
		Session::set("_csrf",$csrf);
		
		return $token;
	}
	
	public function checkCrsf($csrf)
	{
		$_csrf = session::get("_csrf");
		return $csrf == $_csrf;
	}
	
	public function errorHandler(\Exception $e)
	{
		throw $e;
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