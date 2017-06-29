<?php
/**
 * User: nathena
 * Date: 2017/6/19 0019
 * Time: 14:55
 */

namespace base;


use zeus\base\logger\Logger;

class EmptyController extends BaseAppController
{
    public function index(){
        Logger::debug($_SERVER['REQUEST_URI']);
        echo 111;
    }
}