<?php
namespace app\controller;

use zeus\foundation\mvc\Controller;

class Index extends Controller
{
	public function index()
	{
		print_r($_GET);
		print_r($_POST);
		print_r(func_get_args());
		
		print_r($this->request->get());
		print_r($this->request->post());
		
		//print_r($this->application);
		
		$this->application->debug();
	}
}