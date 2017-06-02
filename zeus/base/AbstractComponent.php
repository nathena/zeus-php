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

    /**
     * @param $event
     */
    public function publishMessage(AbstractEvent $event){
        $event->publish();
        $event->callback($this);
    }

    protected function registerComponent(){
        ApplicationContext::currentContext()->registerComponent($this);
    }
}