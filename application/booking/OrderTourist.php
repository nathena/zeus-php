<?php
/**
 * User: nathena
 * Date: 2017/6/20 0020
 * Time: 9:09
 */

namespace booking;


use zeus\domain\AggregateRoot;

class OrderTourist extends AggregateRoot
{
    protected $schema = "t_order_tourist";

    public function __construct($data = null)
    {
        parent::__construct($data);

    }
}