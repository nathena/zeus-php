<?php
/**
 * Created by IntelliJ IDEA.
 * User: nathena
 * Date: 2017/6/2
 * Time: 22:34
 */

namespace zeus\domain;

class DbBaseRepository
{
    /**
     * @var \zeus\database\pdo\Pdo
     */
    protected $db;

    /**
     * @var LocalCacheManager
     */
    protected $localCache;

    public function __construct()
    {
        $this->db = DbBaseRepository::openSession();
        $this->localCache = new LocalCacheManager($this);
    }

}