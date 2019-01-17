<?php
/**
 * User: nathena
 * Date: 2017/6/12 0012
 * Time: 13:57
 */

namespace zeus\base\event;


interface EventListenerInterface
{
    public function handler(EventInterface $event);
}