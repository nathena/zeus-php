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

    private $data = [];
    private $method;

    public function __construct()
    {
        $class = get_class($this);

        $this->eventType = $class;
        $this->eventId = $this->eventType . time();

        $class = get_class($this);
        $class_fragments = explode("\\", $class);
        $simple_class_name = end($class_fragments);

        $this->method = "on{$simple_class_name}";
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

    public function getMethod()
    {
        return $this->method;
    }

    public function setData($data)
    {
        if (is_array($data)) {
            $this->data = array_merge($this->data, $data);
        }
    }

    public function getData()
    {
        return $this->data;
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