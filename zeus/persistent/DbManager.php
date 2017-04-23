<?php
namespace zeus\persistent;

use zeus\persistent\pdo\Pdo;
use zeus\persistent\pdo\XaPdo;
use zeus\sandbox\ConfigManager;

class DbManager
{
	/**
	 * \zeus\persistent\pdo\Pdo
	 * @var array
	 */
	protected static $driver_instances = [];
	
	/**
	 * \zeus\persistent\pdo\Pdo
	 * @var array
	 */
	protected static $xa_driver_instances = [];
	
	/**
	 * 
	 * @param string $alias
	 * @return \zeus\persistent\pdo\Pdo
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
	 * @return \zeus\persistent\pdo\Pdo
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