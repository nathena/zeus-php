<?php
/**
 * 日志
 * @author nathena
 *
 */
namespace zeus\logger;

use zeus\env\Env;
use zeus\http\Request;

class Logger
{
    const ERR    = 'ERR';       
    const WARN   = 'WARN';      
    const NOTICE = 'NOTICE';    
    const INFO   = 'INFO';      
    const DEBUG  = 'DEBUG';
    
    private static function save($message, $level = self::INFO)
    {
    	if( is_dir(Env::config('log_path')) )
    	{
    		$file = Env::config('log_path') .'/'.date('Ymd_') . strtolower($level) . '.log';
    		$log = sprintf('[PID] %s [IP] %s [TIME] %s [MSG] %s',getmypid(), Request::ip(), date('H:i:s'), $message) . "\n";
    		
    		file_put_contents($file, $log, FILE_APPEND|LOCK_EX);
    	}
    }
    
    public static function debug($message)
    {
    	if( 0 >= intval(Env::config('log_level')) )
    	{
    		self::save($message,self::DEBUG);
    	}
    }
    
    public static function info($message)
    {
    	if( 1 >= intval(Env::config('log_level')) )
    	{
        	self::save($message,self::INFO);
    	}
    }
    
    public static function warn($type, $code, $message, $file, $line)
    {
        if( 2 >= intval(Env::config('log_level')) )
        {
            $message .= 'type   = '.$type."\n".
                        'code    = '.$code."\n".
                        'message = '.$message."\n".
                        'file    = '.$file."\n".
                        'line    = '.$line."\n";
            
            self::save($message,self::WARN);
        }
    }
    
    public static function error($type, $code, $message, $file, $line)
    {
        if( 3 >= intval(Env::config('log_level')) )
        {
            $message .= 'type   = '.$type."\n".
                        'code    = '.$code."\n".
                        'message = '.$message."\n".
                        'file    = '.$file."\n".
                        'line    = '.$line."\n";
            
             self::save($message,self::ERR);
        }
    }
}