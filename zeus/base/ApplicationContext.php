<?php

namespace zeus\sandbox;

use zeus\base\command\CommandBus;
use zeus\base\event\EventBus;
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
    private $containers = [];

    /**
     * @return \zeus\sandbox\ApplicationContext
     */
    public static function currentContext()
    {
        if (!isset(static::$context)) {
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
        echo '<br>', microtime(true) - ZEUS_START_TIME;
        echo '<br>', memory_get_usage() - ZEUS_START_MEM;
        echo '<br>';
        print_r(get_included_files());
    }

    /**
     * @return EventBus
     */
    public function getEventBus()
    {
        return EventBus::getInstance();
    }

    /**
     * @return CommandBus
     */
    public function getCommandBus()
    {
        return CommandBus::getInstance();
    }

    public function ip()
    {
        if (static::isCgi()) {
            return $this->getCgiIp();
        }

        if (static::isCli()) {
            return $this->getCliIp();
        }

        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos = array_search('unknown', $arr);
            if (false !== $pos) {
                unset($arr[$pos]);
            }
            $server_ip = trim($arr[0]);
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $server_ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $server_ip = $_SERVER['REMOTE_ADDR'];
        } else {
            $server_ip = getenv('SERVER_ADDR');
        }
        return $server_ip;
    }

    public function register($obj)
    {
        $this->containers[get_class($obj)] = $obj;
    }

    public function getInstance($clazz, $prototype = false)
    {
        if (!$prototype) {
            return new $clazz();
        }
        if (!isset($this->containers[$clazz])) {
            $this->containers[$clazz] = new $clazz();
        }
        return $this->containers[$clazz];
    }

    public function start()
    {
        if (defined("APP_ENV_PATH")) {
            if (is_file(APP_ENV_PATH)) {
                $app_config = include_once APP_ENV_PATH;
                if(!empty($app_config)){
                    ConfigManager::getInstance()->setConfig($app_config);
                }
            }
        }
        //timezone
        date_default_timezone_set(empty(ConfigManager::config('default_timezone')) ? 'Asia/Shanghai' : ConfigManager::config('default_timezone'));
        //upload
        $upload_tmp_dir = ConfigManager::config("upload_tmp_dir");
        if (!empty($upload_tmp_dir)) {
            ini_set("upload_tmp_dir", $upload_tmp_dir);
        }
        $upload_max_filesize = ConfigManager::config("upload_max_filesize");
        if (!empty($upload_max_filesize)) {
            ini_set("upload_max_filesize", $upload_max_filesize);
        }
    }

    private function __construct()
    {
        include_once "Autoloader.php";
        include_once "ConfigManager.php";

        Autoloader::getInstance()->registerNamespaces('zeus', ZEUS_PATH);
    }

    private function getCgiIp()
    {
        if (getenv('HTTP_CLIENT_IP')) {
            $client_ip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
            $client_ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('REMOTE_ADDR')) {
            $client_ip = getenv('REMOTE_ADDR');
        } else {
            $client_ip = $_SERVER['REMOTE_ADDR'];
        }
        return $client_ip;
    }

    private function getCliIp()
    {
        //TODO
        return "0.0.0.0";
    }
}

//set_exception_handler&set_error_handler
function __error_handler($errno, $errstr, $errfile = '', $errline = '', $errcontext = null)
{
    $message = 'type   = PHP ERROR' . "\n" .
        'code    = ' . $errno . "\n" .
        'message = ' . $errstr . "\n" .
        'file    = ' . $errfile . "\n" .
        'line    = ' . $errline . "\n";

    __errorMsgHandler($errno, $message);
}

function __exception_handler($exception)
{
    $type = get_class($exception);
    $code = $exception->getCode();
    $message = $exception->getMessage() . "\n" . $exception->getTraceAsString();
    $file = $exception->getFile();
    $line = $exception->getLine();

    $message = 'type   = ' . $type . "\n" .
        'code    = ' . $code . "\n" .
        'message = ' . $message . "\n" .
        'file    = ' . $file . "\n" .
        'line    = ' . $line . "\n";

    __errorMsgHandler($code, $message);
}

function __errorMsgHandler($code, $message)
{
    Logger::error($message);
    //应用没有处理错误
    $str = '<style>body {font-size:12px;}</style>';
    $str .= '<h1>操作失败！</h1><br />';
    $str .= '<strong>错误信息：<strong><font color="red">' . $message . '</font><br />';

    echo $str;
    exit($code);
}

//异常处理
set_error_handler(__NAMESPACE__ . '\__error_handler');
set_exception_handler(__NAMESPACE__ . '\__exception_handler');
