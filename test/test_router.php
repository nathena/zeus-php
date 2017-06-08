<?php
namespace test_router;
/**
 * User: nathena
 * Date: 2017/6/8 0008
 * Time: 14:34
 */
use zeus\mvc\Application;

include_once 'bootstrap.php';

Application::getInstance()->dispatch("/test?a=1&b=2");