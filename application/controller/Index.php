<?php
namespace app\controller;

use zeus\foundation\mvc\Controller;
use zeus\foundation\store\DbManager;

class Index extends Controller
{
	public function index()
	{
		echo __CLASS__.' => '.__METHOD__;
		
		$pdo = DbManager::openSession();
		
		try 
		{
			$pdo->beginTransaction();
			
			$pdo->insert("test",array("aaa"=>2,'sign_key'=>2));
			//$pdo->insert("test",array("aaaa"=>2,'sign_key'=>2));
			
			$result = $pdo->query("select * from finance_tft_rechange");
			
			print_r($result);
			
			$pdo->commit();
		}
		catch(\Exception $e)
		{
			$pdo->rollback();
			print_r($e);
			echo $e->getMessage();
		}
		
	}
}