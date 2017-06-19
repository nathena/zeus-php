<?php
/**
 * User: nathena
 * Date: 2017/6/19 0019
 * Time: 16:05
 */

namespace booking;


use zeus\domain\AbstractEntity;

class OrderCheck extends AbstractEntity
{
    protected $schema = "t_order_check";

    private $store_engine;

    public function __construct($data = null)
    {
        parent::__construct($data);

        $this->store_engine = OrderRepository::getInstance();
    }


}