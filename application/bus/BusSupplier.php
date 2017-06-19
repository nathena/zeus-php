<?php
namespace bus;
use zeus\domain\AggregateRoot;

/**
 * User: nathena
 * Date: 2017/6/19 0019
 * Time: 20:24
 */
class BusSupplier extends AggregateRoot
{
    protected $schema = "t_supplier";

    public function __construct($data = null)
    {
        parent::__construct($data);
    }
}