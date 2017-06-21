<?php
/**
 * User: nathena
 * Date: 2017/6/19 0019
 * Time: 15:27
 */

namespace account;


use zeus\database\DbManager;
use zeus\database\DmlType;
use zeus\database\specification\AbstractSpecification;
use zeus\database\specification\QueryRowSpecification;
use zeus\database\specification\SimpleSpecification;
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

    public function load(AbstractSpecification $specification)
    {
        $result = [];
        $data = parent::load($specification);
        if (!empty($data)) {
            if (DmlType::DML_SELECT_ONE) {
                $data = [$data];
                foreach ($data as $item) {
                    $result[] = new Account($item);
                }
            } else {
                $result = new Account($data);
            }
        }
        return $result;
    }

    public function getById($id){
        $spec = new QueryRowSpecification();
        $spec->from("t_account")->where("id",$id);

        $data = $this->openSession()->execute($spec);
        if(!empty($data)){
            return new Account($data);
        }
        return null;
    }

    public function getByUserName($name){
        $spec = new QueryRowSpecification();
        $spec->from("t_account")->where("user_name",trim($name));

        $data = $this->openSession()->execute($spec);
        if(!empty($data)){
            return new Account($data);
        }
        return null;
    }
}