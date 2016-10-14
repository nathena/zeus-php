<?php
namespace zeus\store\db;

use zeus\etc\ConfigManager;
use zeus\store\db\pdo\Pdo;
use zeus\store\db\pdo\XaPdo;

class DbManager
{
	protected static $driver_instances = [];
	protected static $xa_driver_instances = [];
	
	/**
	 * 
	 * @param string $alias
	 * @return \zeus\store\db\driver\Pdo
	 */
	public static function openSession($alias='default')
	{
		if( !isset(self::$driver_instances[$alias]) )
		{
			self::$driver_instances[$alias] = new Pdo(ConfigManager::database());
		}
		
		return self::$driver_instances[$alias];
	}
	
	/**
	 * 
	 * @param string $xid
	 * @return \zeus\store\db\driver\Pdo
	 */
	public static function openXaSession($config,$xid)
	{
		$instance = new XaPdo($config,$xid);
		
		self::$xa_driver_instances[] = $instance;
		
		return $instance;
	}
	
	public static function getAllSessions()
	{
		return self::$driver_instances;
	}
	
	public static function getAllXaSessions()
	{
		return self::$xa_driver_instances;
	}
}