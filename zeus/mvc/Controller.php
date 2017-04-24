<?php
namespace zeus\mvc;

abstract class Controller
{
	protected $view;
	
	protected $request;
	protected $response;
	
	protected $application;
	
	public function __construct()
	{
		$this->application = Application::getInstance();
		
		$this->request     = $this->application->getRequest();
		$this->response    = $this->application->getResponse();
		
		$this->view        = $this->application->getView();
	}
	
	public function errorHandler(\Exception $e)
	{
		throw $e;
	}
	
}