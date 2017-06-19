<?php
namespace account_auth;
use zeus\domain\AbstractEntity;

/**
 * User: nathena
 * Date: 2017/6/19 0019
 * Time: 20:31
 */
class RolePrivilege extends AbstractEntity
{
    protected $schema = "t_role_privilege";

    public function __construct($data = null)
    {
        parent::__construct($data);
    }
}

