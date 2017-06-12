<?php
namespace zeus\base;
use zeus\database\DbManager;

/**
 * User: nathena
 */
abstract class AbstractQuery
{
    protected $db;

    public function __construct()
    {
        $this->db = DbManager::openSession();
    }

}