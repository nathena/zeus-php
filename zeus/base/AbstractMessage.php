<?php
namespace zeus\base;
use zeus\base\exception\ClassMethodNotFoundException;

/**
 * Created by IntelliJ IDEA.
 * User: nathena
 * Date: 2017/6/2 0002
 * Time: 10:39
 */
abstract class AbstractMessage
{
    private static $handlers = [];
    protected $data = [];

    protected $msgId;
    protected $msgType;
    protected $method;

    private $result;

    public function subscribe($msgHandler){
        if(!isset(static::$handlers[$this->msgType])){
            static::$handlers[$this->msgType] = [];
        }
        static::$handlers[$this->msgType][] = $msgHandler;
    }

    public function publish(){
        $this->start();
        $handlers = static::handlers[$this->msgType];
        foreach($handlers as $handler){
            if(!empty($handler) && class_exists($handler)){
                $caller = new $handler();
                if(is_object($caller) && method_exists($caller, $this->method)){
                    $handler->{$this->method}($this);
                }else{
                    throw new ClassMethodNotFoundException($handler.':'.$this->method);
                }
            }
        }
        $this->finished();
    }

    public function toArray()
    {
        return $this->data;
    }

    public function setResult($result){
        $this->result = $result;
    }

    public function getResult(){
        return $this->result;
    }

    public function __get($key){
        if(isset($this->data[$key])){
            return $this->data[$key];
        }
        return null;
    }
    public function __set($key,$val){
        $this->data[$key] = $val;
    }

    protected function start(){
    }

    protected function finished(){
    }
}