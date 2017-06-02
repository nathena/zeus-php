<?php
namespace zeus\base;
/**
 * Created by IntelliJ IDEA.
 * User: nathena
 * Date: 2017/6/2 0002
 * Time: 10:16
 */
abstract class AbstractComponent
{
    public function __construct($singleton=true)
    {
        if($singleton){
            ApplicationContext::currentContext()->registerComponent($this);
        }
    }

    /**
     * @param $event
     */
    public function publishMessage(AbstractEvent $event){
        $event->publish();
        $event->callback($this);
    }
}