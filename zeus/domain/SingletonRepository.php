<?php
/**
 * Created by IntelliJ IDEA.
 * User: nathena
 * Date: 2017/6/6 0006
 * Time: 14:46
 */

namespace zeus\domain;

abstract class SingletonRepository
{
    private static $instance = [];

    public static function getInstance($cacheType){
        if(!isset(self::$instance[static::class])){
            self::$instance[static::class] = new static($cacheType);
        }
        return self::$instance[static::class];
    }

    protected function __construct()
    {
    }
}