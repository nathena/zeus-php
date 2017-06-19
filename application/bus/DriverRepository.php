<?php
/**
 * User: nathena
 * Date: 2017/6/19 0019
 * Time: 20:29
 */

namespace bus;


use zeus\domain\GeneralDbRepository;

class DriverRepository extends GeneralDbRepository
{
    private static $instance;

    /**
     * @return DriverRepository
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
}