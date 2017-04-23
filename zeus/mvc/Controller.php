<?php
namespace zeus\mvc;

class Controller
{
	protected $view;
	
	protected $request;
	protected $reponse;
	
	protected $application;
	
	public function __construct()
	{
		$this->application = Application::getCurrentApplication();
		
		$this->request = $this->application->getRequest();
		$this->reponse = $this->application->getReponse();
		
		$this->view    = $this->application->getView();
	}
	
	public function csrf($csrf='')
	{
		if( empty($csrf) ){
			$csrf = UUIDGenerator::randChar(5);
			Session::set("_csrf",$csrf);
			return $token;
		}
		
		$_csrf = Session::get("_csrf");
		return $csrf == $_csrf;
	}
	
	public function errorHandler(\Exception $e)
	{
		throw $e;
	}
}