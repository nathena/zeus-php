<?php
namespace zeus\db;

use zeus\db\driver\Pdo;
use zeus\env\Env;
use zeus\exception\UnSupportDbDriverException;

class DbManager
{
	public static $driver_instances = [];
	public static $xa_driver_instances = [];
	
	public static function openSession($alias='default')
	{
		if( !isset(self::$driver_instances[$alias]) )
		{
			$config = Env::database();
			
			$type = $config['type'];
			if( 'pdo'!=$type )
			{
				throw new UnSupportDbDriverException($type);
			}
			
			$driver = 'zeus\\db\\driver\\'.ucfirst($type);
			
			self::$driver_instances[$alias] = new $driver($config[$type]);
		}
		
		return self::$driver_instances[$alias];
	}
	
	public static function openXaSession($xid)
	{
		$config = Env::database();
		
		$type = $config['type'];
		if( 'pdo'!=$type )
		{
			throw new UnSupportDbDriverException($type);
		}
		
		$driver = 'zeus\\db\\driver\\'.ucfirst($type);
		
		$instance = new $driver($config[$type]);
		$instance->xid($xid);
		
		self::$xa_driver_instances[] = $instance;
		
		return $instance;
	}
}