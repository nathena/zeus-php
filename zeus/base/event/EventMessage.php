<?php
/**
 * User: nathena
 * Date: 2017/6/12 0012
 * Time: 13:47
 */

namespace zeus\base\event;


class EventMessage
{
    protected $event;
    protected $sender;

    private $callable;
    private $msg;

    public function __construct($sender, AbstractEvent $event, callable $callable)
    {
        $this->sender = $sender;
        $this->event = $event;
        $this->callable = $callable;
    }

    public function getEvent()
    {
        return $this->event;
    }

    public function getMsg()
    {
        return $this->msg;
    }

    public function trigger($msg)
    {
        $this->msg = $msg;
        if(is_callable($this->callable)){
            call_user_func_array($this->callable,[$this]);
        }else{
            $method = $this->event->getMethod();
            if (is_object($this->sender) && method_exists($this->sender, $method)) {
                call_user_func_array(array($this->sender,$method),[$this]);
            }
        }
    }
}