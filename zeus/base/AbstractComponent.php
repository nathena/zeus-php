<?php
namespace zeus\base;
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
     * @param $event
     */
    public function publishMessage(AbstractEvent $event){
        $event->publish();
        $event->callback($this);
    }
}