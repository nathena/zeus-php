<?php
/**
 * User: nathena
 * Date: 2017/6/19 0019
 * Time: 16:05
 */

namespace booking;


use zeus\domain\AggregateRoot;

class OrderCheck extends AggregateRoot
{
    protected $schema = "t_order_check";

    public function __construct($data = null)
    {
        parent::__construct($data);
    }

}