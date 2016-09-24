<?php
namespace zeus\mvc;

use zeus\http\Session;
use zeus\http\Request;
use zeus\http\Response;
use zeus\util\UuidHelper;
use zeus\filter\FilterInterface;

class Controller
{
	protected $view = new View();
	protected $request = new Request();
	protected $response = new Response();
	
	protected $filter = null;
	
	public function __request(Request $request)
	{
		$this->request->merge($request);
	}
	
	public function __filter(FilterInterface $filter)
	{
		$this->filter = $filter;
		$params = $this->request->getData();
		foreach( $params as $key => $param )
		{
			$param = $this->filter->doFilter($param);
			
			$params[$key] = $param;
		}
		
		$this->request->merge($params);
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