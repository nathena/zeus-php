<?php
namespace test;
use zeus\base\AbstractEvent;

/**
 * User: nathena
 * Date: 2017/6/12 0012
 * Time: 10:04
 */
class EchoedEvent extends AbstractEvent
{
    public function __construct($data)
    {
        parent::__construct($data);

        $this->subscribe();
    }
}