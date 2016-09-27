<?php
namespace zeus\db;

use zeus\db\driver\Pdo;
use zeus\env\Env;

class DbManager
{
	public static $driver_instances = [];
	public static $xa_driver_instances = [];
	
	/**
	 * 
	 * @param string $alias
	 * @return \zeus\db\driver\Pdo
	 */
	public static function openSession($alias='default')
	{
		if( !isset(self::$driver_instances[$alias]) )
		{
			self::$driver_instances[$alias] = new Pdo(Env::database());
		}
		
		return self::$driver_instances[$alias];
	}
	
	/**
	 * 
	 * @param string $xid
	 * @return \zeus\db\driver\Pdo
	 */
	public static function openXaSession($xid)
	{
		$instance = new Pdo(Env::database());
		$instance->xid($xid);
		
		self::$xa_driver_instances[] = $instance;
		
		return $instance;
	}
}