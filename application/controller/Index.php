<?php
namespace app\controller;

use zeus\mvc\Controller;
use zeus\db\DbManager;
use zeus\db\driver\Pdo;

class Index extends Controller
{
	public function index()
	{
		echo __CLASS__.' => '.__METHOD__;
		
		$pdo = DbManager::openSession();
	}
}