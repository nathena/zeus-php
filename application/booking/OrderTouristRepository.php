<?php
/**
 * User: nathena
 * Date: 2017/6/19 0019
 * Time: 20:18
 */

namespace booking;


use zeus\domain\GeneralDbRepository;

class OrderTouristRepository extends GeneralDbRepository
{
    private static $instance;

    /**
     * @return OrderTouristRepository
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