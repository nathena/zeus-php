<?php
/**
 * User: nathena
 * Date: 2017/6/19 0019
 * Time: 20:27
 */

namespace bus;


use zeus\domain\AggregateRoot;

class Bus extends AggregateRoot
{
    protected $schema = "t_bus";

    public function __construct($data = null)
    {
        parent::__construct($data);
    }
}