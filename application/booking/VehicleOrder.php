<?php
/**
 * User: nathena
 * Date: 2017/6/19 0019
 * Time: 16:03
 */

namespace booking;

use zeus\domain\AggregateRoot;

class VehicleOrder extends AggregateRoot
{
    protected $schema = "t_order_vehicle";

    public function __construct($data = null)
    {
        parent::__construct($data);
    }
}