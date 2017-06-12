<?php
namespace zeus\base;
/**
 * Created by IntelliJ IDEA.
 * User: nathena
 * Date: 2017/5/27 0027
 * Time: 15:43
 */
abstract class AbstractEvent extends AbstractMessage
{
    public function __construct($data)
    {
        $class = get_class($this);
        $classVal = explode("\\",$class);
        $simpleClassVal = end($classVal);
        $method_name = 'on'.$simpleClassVal;

        $this->msgType = $class;
        $this->msgId = $this->msgType.time();
        $this->method = $method_name;
    }

    public function callback($component){
        if(is_object($component) && method_exists($component, $this->method)){
            $component->{$this->method}($this);
        }
    }
}