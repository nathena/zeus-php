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

    public function __construct($sender,AbstractEvent $event)
    {
        $this->sender = $sender;
        $this->event  = $event;
    }

    public function getEvent()
    {
        return $this->event;
    }

    public function getSender()
    {
        return $this->sender;
    }
}