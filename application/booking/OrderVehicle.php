<?php
/**
 * User: nathena
 * Date: 2017/6/19 0019
 * Time: 16:03
 */

namespace booking;

use zeus\domain\AbstractEntity;

class OrderVehicle extends AbstractEntity
{
    protected $schema = "t_order_vehicle";

    private $store_engine;

    public function __construct($data = null)
    {
        parent::__construct($data);

        $this->store_engine = OrderRepository::getInstance();
    }
}