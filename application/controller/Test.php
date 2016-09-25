<?php
namespace app\controller;

use zeus\mvc\Controller;

class Test extends Controller
{
	public function index()
	{
		print_r(func_get_args());
		echo __CLASS__,'<br />'.__METHOD__,'<br/>';
		
		print_r( $this->request->get() );
	}
}