<?php
namespace zeus\mvc;

use zeus\util\UuidHelper;
use zeus\http\Request;
use zeus\http\Response;
use zeus\filter\FilterInterface;

class Controller
{
	protected $view;
	
	protected $request;
	protected $reponse;
	protected $router;
	protected $filter;
	
	public function __construct()
	{
		$this->view = new View();
		$this->response = Response::create($this);
	}
	
	public function getRequest()
	{
		return $this->request;
	}
	
	public function setRequest(Request $request)
	{
		$this->request = $request;
	}
	
	public function getResponse()
	{
		return $this->response;
	}
	
	public function setResponse(Response $response)
	{
		$this->response = $response;
	}
	
	public function getFilter()
	{
		return $this->filter;
	}
	
	public function setFilter(FilterInterface $filter)
	{
		$this->filter = $filter;
	}
	
	public function setRouter(Router $router)
	{
		$this->router = $router;
		
		$this->request = $router->getRequest();
		$this->filter  = $router->getFilter();
	}
	
	public function getRouter()
	{
		return $this->router;
	}
	
	public function crsf_token()
	{
		$uuid = new UuidHelper();
		$token = $uuid->randChar(5);
		
		Session::set("_token",$token);
		
		return $token;
	}
	
	public function check_crsf_token($token)
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
}