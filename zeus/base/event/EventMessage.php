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

    private $callback_method;

    public function __construct($sender,AbstractEvent $event)
    {
        $this->sender = $sender;
        $this->event  = $event;

        $eventClass = get_class($event);
        $eventClass_fragments = explode("\\",$eventClass);
        $simple_class_name = end($eventClass_fragments);

        $this->callback_method = "on{$simple_class_name}";
    }

    public function getEvent()
    {
        return $this->event;
    }

    public function trigger($msg)
    {
        if(is_object($this->sender) && method_exists($this->sender,$this->callback_method))
        {
            $this->sender->{$this->callback_method}($this->event,$msg);
        }

    }
}