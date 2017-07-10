<?php
/**
 * User: nathena
 * Date: 2017/6/29 0029
 * Time: 9:31
 */
namespace boot;

use base\EmptyController;
use base\IndexPlatformController;
use passport\LoginController;
use passport\LogoutController;
use zeus\mvc\Application;
use zeus\mvc\Router;
use zeus\sandbox\Autoloader;

define("__APPLICATION__",dirname(__FILE__));
define("__ROOT__",dirname(__APPLICATION__));
define("__WEBROOT__",__ROOT__.DIRECTORY_SEPARATOR."webroot");
define("APP_ENV_PATH",__APPLICATION__.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php');

include_once __ROOT__.DIRECTORY_SEPARATOR."zeus".DIRECTORY_SEPARATOR."bootstrap.php";
Autoloader::getInstance()->registerDirs([__APPLICATION__]);

//defout
Router::addRouter('/',IndexPlatformController::class);
Router::addRouter('/favicon.ico',EmptyController::class);

//登录
Router::addRouter('/login',LoginController::class."@login");
Router::addRouter('/do_login',LoginController::class."@do_login");
//退出
Router::addRouter('/logout',LogoutController::class);

Application::getInstance()->dispatch($_SERVER['REQUEST_URI']);
