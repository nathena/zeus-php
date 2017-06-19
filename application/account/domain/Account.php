<?php
/**
 * User: nathena
 * Date: 2017/6/19 0019
 * Time: 10:38
 */

namespace account\domain;


use zeus\domain\AggregateRoot;
use zeus\domain\GeneralDbRepository;

class Account extends AggregateRoot
{
    protected $schema = "t_account";

    private $store_engine;

    public function __construct($data = null)
    {
        parent::__construct($data);

        $this->store_engine = AccountRepository::getInstance();
    }

    //验证密码是否正确
    public function check_passwd($passwd)
    {
        $_passwd = md5($passwd.$this->data['user_name']);

        return $_passwd === $passwd;
    }

    //更新数据
    public function update_info(array $data)
    {
        if(empty($data)){
            return 0;
        }

        $this->setData($data);

        $rowCount = $this->store_engine->save($this);
        $this->update_properties();

        return $rowCount;
    }


}