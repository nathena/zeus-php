<?php
namespace zeus\store\db;

use zeus\etc\ConfigManager;
use zeus\store\db\pdo\Pdo;
use zeus\store\db\pdo\XaPdo;

class DbManager
{
	public static $driver_instances = [];
	public static $xa_driver_instances = [];
	
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
	public static function openXaSession($xid)
	{
		$instance = new XaPdo(ConfigManager::database(),$xid);
		
		self::$xa_driver_instances[] = $instance;
		
		return $instance;
	}
}