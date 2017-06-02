<?php
/**
 * Created by IntelliJ IDEA.
 * User: nathena
 * Date: 2017/6/2
 * Time: 22:34
 */

namespace zeus\database;


use zeus\base\LocalCacheRepository;

class DbRepository extends LocalCacheRepository
{
    protected $db;

    public function __construct()
    {
        parent::__construct();

        $this->db = DbManager::openSession();

    }
}