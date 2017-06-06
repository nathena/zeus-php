<?php
/**
 * Created by IntelliJ IDEA.
 * User: nathena
 * Date: 2017/6/6 0006
 * Time: 14:07
 */

namespace zeus\http;

use zeus\http\exception\InitSessionSaveHandlerException;
use zeus\sandbox\ConfigManager;

class Session
{
    private static $instance = null;
    private $sessionId;

    /**
     * @return Session
     */
    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new Session();
        }
        return self::$instance;
    }

    protected function __construct()
    {
        $handler = ConfigManager::config("session.save_handler");
        if (!empty($handler) && !class_exists($handler) || !session_set_save_handler(new $handler()))
        {
            throw new InitSessionSaveHandlerException('error session handler:' . $handler);
        }

        $var_session_id = ConfigManager::config("session.var_session_id");
        if(!empty($var_session_id)){
            session_id($var_session_id);
        }

        $session_name = ConfigManager::config("session.session_name");
        if(!empty($session_name)){
            session_name($session_name);
        }

        $session_save_path = ConfigManager::config("session.session_save_path");
        if(!empty($session_save_path) && is_dir($session_save_path) && is_writable($session_save_path)){
            session_save_path($session_save_path);
        }

        ini_set('session.use_trans_sid',0);//关闭透明session
        ini_set('session.cookie_httponly',1);//开启httponnly

        session_start();
        $this->sessionId = session_id();
    }

    public function getId()
    {
        return $this->sessionId;
    }

    public function regenerateId()
    {
        session_regenerate_id(true);
        $this->sessionId = session_id();
    }

    public function kill()
    {
        $_SESSION = null;
        session_unset();
        session_destroy();
        unset($this->sessionId);
    }

    public function __set($name, $value)
    {
        $_SESSION[$name] = $value;
    }

    public function __get($name)
    {
        return (isset($_SESSION[$name])) ? $_SESSION[$name] : null;
    }

    public function __isset($name)
    {
        return isset($_SESSION[$name]);
    }

    public function __unset($name)
    {
        $_SESSION[$name] = null;
        unset($_SESSION[$name]);
    }
}