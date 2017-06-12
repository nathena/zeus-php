<?php
/**
 * Class AbstractEvent
 * @package zeus\base\event
 */

namespace zeus\base\event;

abstract class AbstractEvent
{
    protected $eventId;
    protected $eventType;

    private $result = [];
    private $data = [];

    public function __construct()
    {
        $class = get_class($this);

        $this->eventType = $class;
        $this->eventId = $this->eventType . time();
    }

    public function start()
    {

    }

    public function finished()
    {
    }

    public function getEventType()
    {
        return $this->eventType;
    }

    public function getEventId()
    {
        return $this->eventId;
    }

    public function setData($data)
    {
        if(is_array($data)){
            $this->data = array_merge($this->data, $data);
        }
    }

    public function getData()
    {
        return $this->data;
    }

    public function setResult($result)
    {
        if(!is_array($result)){
            $result = [$result];
        }
        $this->result = array_merge($this->result,$result);
    }

    public function getResult()
    {
        return $this->result;
    }

    public function __get($key)
    {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }
        return null;
    }

    public function __set($key, $val)
    {
        $this->data[$key] = $val;
    }


}