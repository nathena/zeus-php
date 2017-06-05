<?php
namespace zeus\database;

use zeus\database\pdo\Pdo;
use zeus\database\pdo\XaPdo;
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
	 * @param string $database
	 * @return \zeus\database\pdo\Pdo
	 */
	public static function openSession($database = "database")
	{
		if( !isset(self::$driver_instances[$database]) )
		{
			$config = ConfigManager::config($database);
            if(empty($config)){
                throw new DbCofigNotFoundException("Pdo {$database} 配置文件找不到.");
            }

            $cfg = [
                "dsn" => $config["{$database}.pdo.dsn"],
                "user" => $config["{$database}.pdo.user"],
                "pass" => $config["{$database}.pdo.pass"],
                "charset" => $config["{$database}.pdo.charset"],
            ];

			self::$driver_instances[$database] = new Pdo($cfg);
		}
		
		return self::$driver_instances[$database];
	}
	
	/**
	 * 
	 * @param string $xa_database
	 * @return \zeus\database\pdo\Pdo
	 */
	public static function openXaSession($xa_database)
	{
        if( !isset(self::$xa_driver_instances[$xa_database]) )
        {
            $config = ConfigManager::config($xa_database);
            if(empty($config)){
                throw new DbCofigNotFoundException("Pdo {$xa_database} 配置文件找不到.");
            }

            $cfg = [
                "dsn" => $config["{$xa_database}.pdo.dsn"],
                "user" => $config["{$xa_database}.pdo.user"],
                "pass" => $config["{$xa_database}.pdo.pass"],
                "charset" => $config["{$xa_database}.pdo.charset"],
            ];

            self::$xa_driver_instances[$xa_database] = new XaPdo($cfg);
        }

		return self::$driver_instances[$xa_database];
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