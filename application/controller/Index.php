<?php
namespace app\controller;

use zeus\mvc\Controller;

class Index extends Controller
{
	public function index()
	{
		echo __CLASS__.' => '.__METHOD__;
	}
}