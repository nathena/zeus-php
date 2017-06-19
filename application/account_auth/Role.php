<?php
namespace account_auth;
use zeus\domain\AggregateRoot;

/**
 * User: nathena
 * Date: 2017/6/19 0019
 * Time: 20:31
 */
class Role extends AggregateRoot
{
    protected $schema = "t_role";

    public function __construct($data = null)
    {
        parent::__construct($data);
    }
}

