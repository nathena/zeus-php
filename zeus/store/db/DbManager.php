<?php
namespace zeus\store\db;

use zeus\etc\ConfigManager;
use zeus\store\db\pdo\Pdo;
use zeus\store\db\pdo\XaPdo;

class DbManager
{
	/**
	 * \zeus\store\db\pdo\Pdo
	 * @var array
	 */
	protected static $driver_instances = [];
	
	/**
	 * \zeus\store\db\pdo\Pdo
	 * @var array
	 */
	protected static $xa_driver_instances = [];
	
	/**
	 * 
	 * @param string $alias
	 * @return \zeus\store\db\pdo\Pdo
	 */
	public static function openSession($alias='default')
	{
		if( !isset(self::$driver_instances[$alias]) )
		{
			$config = ConfigManager::database();
			$type = isset($config['type'])?trim($config['type']):'pdo';
			$config = $config[$type];
			
			self::$driver_instances[$alias] = new Pdo($config);
		}
		
		return self::$driver_instances[$alias];
	}
	
	/**
	 * 
	 * @param string $xid
	 * @return \zeus\store\db\pdo\Pdo
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