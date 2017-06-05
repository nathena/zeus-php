<?php
namespace zeus\sandbox;
use zeus\base\exception\ClassNotFoundException;
use zeus\base\logger\Logger;

/**
 * Created by IntelliJ IDEA.
 * User: nathena
 * Date: 2017/6/2 0002
 * Time: 10:02
 */
class ApplicationContext
{
    private static $context;
    private $loader;
    private $containers = [];


    /**
     * @return \zeus\sandbox\ApplicationContext
     */
    public static function currentContext(){
        if(!isset(static::$context)){
            static::$context = new static();
        }
        return static::$context;
    }

    public static function isCli()
    {
        return stripos(PHP_SAPI, 'cli') === 0;
    }

    public static function isCgi()
    {
        return stripos(PHP_SAPI, 'cgi') === 0;
    }

    public static function debug()
    {
        echo '<hr>';
        echo '<br>',microtime(true)-ZEUS_START_TIME;
        echo '<br>',memory_get_usage()-ZEUS_START_MEM;
        echo '<br>';
        print_r(get_included_files());
    }

    public function ip()
    {
        if( static::isCgi()){
            return $this->getCgiIp();
        }

        if(static::isCli()){
            return $this->getCliIp();
        }

        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos = array_search('unknown', $arr);
            if (false !== $pos){
                unset($arr[$pos]);
            }
            $server_ip = trim($arr[0]);
        }elseif (isset($_SERVER['HTTP_CLIENT_IP'])){
            $server_ip = $_SERVER['HTTP_CLIENT_IP'];
        }elseif (isset($_SERVER['REMOTE_ADDR'])){
            $server_ip = $_SERVER['REMOTE_ADDR'];
        }else {
            $server_ip = getenv('SERVER_ADDR');
        }
        return $server_ip;
    }

    public function registerComponent($obj){
        $this->containers[get_class($obj)] = $obj;
    }

    public function getComponent($clazz,$prototype=false){
        if(!$prototype){
            return new $clazz();
        }
        if(!isset($this->containers[$clazz])){
            $this->containers[$clazz] = new $clazz();
        }
        return $this->containers[$clazz];
    }

    public function start(){
        //timezone
        date_default_timezone_set(empty(ConfigManager::config('time_zone')) ? 'Asia/Shanghai' : ConfigManager::config('time_zone'));
    }

    private function __construct()
    {
        $this->loader = new Autoloader();
        $this->loader->registerNamespaces('zeus', ZEUS_PATH);

        ConfigManager::init();

        $components = ConfigManager::config("app_ns");
        foreach( $components as $ns => $path )
        {
            if( is_dir($path) )
            {
                $this->loader->registerNamespaces($ns, $path);
                $components_url_ini = $path.DS."url.ini";
                if(is_file($components_url_ini)){
                    $url_ini = parse_ini_file($components_url_ini,false);
                    ConfigManager::addRouter($url_ini);
                }
            }
        }
    }

    private function getCgiIp(){
        if(getenv('HTTP_CLIENT_IP')){
            $client_ip = getenv('HTTP_CLIENT_IP');
        } elseif(getenv('HTTP_X_FORWARDED_FOR')) {
            $client_ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif(getenv('REMOTE_ADDR')) {
            $client_ip = getenv('REMOTE_ADDR');
        } else {
            $client_ip = $_SERVER['REMOTE_ADDR'];
        }
        return $client_ip;
    }

    private function getCliIp(){
        //TODO
        return "0.0.0.0";
    }
}

//set_exception_handler&set_error_handler
function __error_handler($errno, $errstr, $errfile='', $errline='', $errcontext=null){

    $message  = 'type   = PHP ERROR'."\n".
        'code    = '.$errno."\n".
        'message = '.$errstr."\n".
        'file    = '.$errfile."\n".
        'line    = '.$errline."\n";

    __errorMsgHandler($errno,$message);
}

function __exception_handler($exception){
    $type     = get_class($exception);
    $code     = $exception->getCode();
    $message  = $exception->getMessage()."\n".$exception->getTraceAsString();
    $file     = $exception->getFile();
    $line     = $exception->getLine();

    $message  = 'type   = '.$type."\n".
        'code    = '.$code."\n".
        'message = '.$message."\n".
        'file    = '.$file."\n".
        'line    = '.$line."\n";

    __errorMsgHandler($code,$message);
}

function __errorMsgHandler($code,$message){

    Logger::error($message);

    //应用没有处理错误
    $str = '<style>body {font-size:12px;}</style>';
    $str .= '<h1>操作失败！</h1><br />';
    $str .= '<strong>错误信息：<strong><font color="red">' . $message . '</font><br />';

    echo $str;
    exit($code);
}

//异常处理
set_error_handler(__NAMESPACE__.'\__error_handler');
set_exception_handler(__NAMESPACE__.'\__exception_handler');