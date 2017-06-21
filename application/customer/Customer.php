<?php
/**
 * User: nathena
 * Date: 2017/6/20 0020
 * Time: 9:03
 */

namespace customer;


use zeus\domain\AggregateRoot;

class Customer extends AggregateRoot
{
    protected $schema = "t_customer";

    public function __construct($data = null)
    {
        parent::__construct($data);
    }
}