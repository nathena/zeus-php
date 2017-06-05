<?php
/**
 * 日志
 * @author nathena
 *
 */
namespace zeus\base\logger;

use zeus\sandbox\ApplicationContext;
use zeus\sandbox\ConfigManager;

class Logger
{
    private static $ERR    = 'ERROR';
    private static $WARN   = 'WARN';
    private static $NOTICE = 'NOTICE';
    private static $INFO   = 'INFO';
    private static $DEBUG  = 'DEBUG';

    public static function debug($message)
    {
    	if( 0 >= intval(ConfigManager::config('log.level')) )
    	{
    		self::save($message,self::$DEBUG);
    	}
    }

    public static function info($message)
    {
    	if( 1 >= intval(ConfigManager::config('log.level')) )
    	{
        	self::save($message,self::$INFO);
    	}
    }

    public static function warn($message)
    {
        if( 2 >= intval(ConfigManager::config('log.level')) )
        {
            self::save($message,self::$WARN);
        }
    }

    public static function notice($message)
    {
        if( 3 >= intval(ConfigManager::config('log.level')) )
        {
            self::save($message,self::$NOTICE);
        }
    }
    
    public static function error($message)
    {
        if( 4 >= intval(ConfigManager::config('log.level')) )
        {
             self::save($message,self::$ERR);
        }
    }

    private static function save($message, $level)
    {
        if( is_dir(ConfigManager::config('log.path')) ){
            $file = ConfigManager::config('log.path') .'/'.date('Ymd_').'_log.log';
            $log = sprintf('[PID] %s %s [TIME] %s [IP] %s [MSG] %s',
                    getmypid(),
                    $level,
                    date('H:i:s'),
                    ApplicationContext::currentContext()->ip(),
                    $message) . "\n";

            file_put_contents($file, $log, FILE_APPEND|LOCK_EX);
        }
    }
}