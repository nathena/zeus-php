<?php
/**
 * Class AbstractEvent
 * @package zeus\base\event
 */

namespace zeus\base\event;

interface EventInterface
{
    public function getListenerList();
}