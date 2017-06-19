<?php
/**
 * User: nathena
 * Date: 2017/6/19 0019
 * Time: 15:27
 */

namespace account\domain;


use zeus\database\DbManager;
use zeus\domain\GeneralDbRepository;

class AccountRepository extends GeneralDbRepository
{
    private static $instance;

    /**
     * @return AccountRepository
     */
    public static function getInstance()
    {
        if(!isset(self::$instance)){
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function openSession()
    {
        return DbManager::openSession();
    }

    /**
     * create account factory
     * @param $data
     * @return Account
     */
    public function createAccount($data){

        $account = new Account($data);
        $this->save($account);

        return $account;
    }
}