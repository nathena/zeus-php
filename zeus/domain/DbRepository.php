<?php
/**
 * Created by IntelliJ IDEA.
 * User: nathena
 * Date: 2017/6/2
 * Time: 22:34
 */

namespace zeus\domain;

use zeus\database\DbManager;

class DbRepository extends LocalCacheRepository
{
    /**
     * @var \zeus\database\pdo\Pdo
     */
    protected $db;

    protected function __construct()
    {
        parent::__construct();
        $this->db = DbManager::openSession();
    }

}