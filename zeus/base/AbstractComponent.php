<?php

namespace zeus\base;

use zeus\base\event\EventInterface;
use zeus\base\event\EventMessage;
use zeus\sandbox\ApplicationContext;

/**
 * Created by IntelliJ IDEA.
 * User: nathena
 * Date: 2017/6/2 0002
 * Time: 10:16
 */
abstract class AbstractComponent
{
    /**
     * @param EventInterface $event
     * @param callable|null $callable
     */
    protected function raise(EventInterface $event, callable $callable = null)
    {
        ApplicationContext::currentContext()->getEventBus()->publish(new EventMessage($this, $event,$callable));
    }
}