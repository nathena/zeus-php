<?php
/**
 * User: nathena
 * Date: 2017/6/19 0019
 * Time: 10:38
 */

namespace account\domain;


use zeus\domain\AggregateRoot;

class Account extends AggregateRoot
{
    protected $schema = "t_account";

    public function check_passwd($passwd)
    {
        $_passwd = md5($passwd.$this->data['user_name']);

        return $_passwd === $passwd;
    }
}