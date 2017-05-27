<?php
namespace zeus\domain;
/**
 * Created by IntelliJ IDEA.
 * User: nathena
 * Date: 2017/5/27 0027
 * Time: 15:43
 */
abstract class AbstractDomainEvent
{
    public function __construct($caller)
    {
        $class = get_class($this);
        $classVal = explode("\\",$class);
        $simpleClassVal = end($classVal);

        $method_name = 'on'.$simpleClassVal;

        $this->eventType = $class;
        $this->eventId = $this->eventType.time();
        $this->method = $method_name;

        $this->caller = $caller;
    }

    public function getCaller(){
        return $this->sender;
    }

    public function callback($data=null){
        if(is_object($this->caller) && method_exists($this->caller, $this->method)){
            $this->caller->{$this->method}($this,$data);
        }
    }

    public function subscribe($eventHandler){
        EventPublisher::getInstance()->subscribe($this->eventType, $eventHandler);
    }

    public function publish(){
        EventPublisher::getInstance()->publish($this);
    }

    public function getData()
    {
        return $this->data;
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

    private function handler($handler){
        if(is_object($handler) && method_exists($handler, $this->method)){
            $handler->{$this->method}($this);
        }
    }

    protected $eventId;
    protected $eventType;
    protected $caller;
    protected $method;
    protected $data = [];
}