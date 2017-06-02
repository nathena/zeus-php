<?php
namespace zeus\base;
use zeus\base\exception\ClassNotFoundException;

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
     * @return \zeus\base\ApplicationContext
     */
    public static function currentContext(){
        if(!isset(static::$context)){
            static::$context = new static();
        }
        return static::$context;
    }

    public function registerComponent($obj){
        $this->containers[get_class($obj)] = $obj;
    }

    public function getComponent($clazz,$prototype=false){
        if(!$prototype){
            return new $clazz();
        }
        if(isset($this->containers[$clazz])){
            return $this->containers[$clazz];
        }
        throw new ClassNotFoundException("未找到对应的{$clazz}实例");
    }

    private function __construct()
    {
    }
}