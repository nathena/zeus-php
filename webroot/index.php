<?php
namespace index;
/**
 * User: nathena
 * Date: 2017/6/19 0019
 * Time: 9:49
 */

use zeus\mvc\Application;

define("__WEBROOT__",dirname(__FILE__));
define("__ROOT__",dirname(__WEBROOT__));
define("APP_ENV_PATH",__ROOT__.DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR.'config.php');

include_once __ROOT__.DIRECTORY_SEPARATOR."zeus".DIRECTORY_SEPARATOR."bootstrap.php";

Application::getInstance()->dispatch($_SERVER['REQUEST_URI']);