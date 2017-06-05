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
    const ERR    = 'ERR';       
    const WARN   = 'WARN';      
    const NOTICE = 'NOTICE';    
    const INFO   = 'INFO';      
    const DEBUG  = 'DEBUG';
    
    public static function debug($message)
    {
    	if( 0 >= intval(ConfigManager::config('log.level')) )
    	{
    		self::save($message,self::DEBUG);
    	}
    }
    
    public static function info($message)
    {
    	if( 1 >= intval(ConfigManager::config('log.level')) )
    	{
        	self::save($message,self::INFO);
    	}
    }
    
    public static function warn($message)
    {
        if( 2 >= intval(ConfigManager::config('log.level')) )
        {
            self::save($message,self::WARN);
        }
    }
    
    public static function error($message)
    {
        if( 3 >= intval(ConfigManager::config('log.level')) )
        {
             self::save($message,self::ERR);
        }
    }

    private static function save($message, $level)
    {
        if( is_dir(ConfigManager::config('log.path')) ){
            $file = ConfigManager::config('log_path') .'/'.date('Ymd_') . strtolower($level) . '.log';
            $log = sprintf('[PID] %s [TIME] %s [IP] [MSG] %s',
                    getmypid(),
                    date('H:i:s'),
                    ApplicationContext::currentContext()->ip(),
                    $message) . "\n";

            file_put_contents($file, $log, FILE_APPEND|LOCK_EX);
        }
    }
}