<?php
/**
 * Created by IntelliJ IDEA.
 * User: nathena
 * Date: 2017/6/2
 * Time: 23:16
 */

namespace zeus\database\pdo;


class XaIdGenerator
{
    private static $xaId;

    public static function getXaId(){
        if(!isset(self::$xaId)){
            self::$xaId = uniqid("xa-");
        }
        return self::$xaId;
    }
}