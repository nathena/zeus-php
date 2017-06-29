<?php
/**
 * User: nathena
 * Date: 2017/6/29 0029
 * Time: 9:31
 */
namespace boot;

use account\AccountController;
use account\AuthController;
use account\B2BAuthController;
use base\EmptyController;
use base\IndexPlatformController;
use base\WelcomePlatformController;
use zeus\mvc\Application;
use zeus\mvc\Router;
use zeus\sandbox\ApplicationContext;
use zeus\base\ModelOption;
use zeus\sandbox\Autoloader;

define("__APPLICATION__",dirname(__FILE__));
define("__ROOT__",dirname(__APPLICATION__));
define("__WEBROOT__",__ROOT__.DIRECTORY_SEPARATOR."webroot");
define("APP_ENV_PATH",__APPLICATION__.DIRECTORY_SEPARATOR.'config.php');

include_once __ROOT__.DIRECTORY_SEPARATOR."zeus".DIRECTORY_SEPARATOR."bootstrap.php";
Autoloader::getInstance()->registerDirs([__APPLICATION__]);
Autoloader::getInstance()->registerNamespaces("com\\oa",__APPLICATION__);

//defout
Router::addRouter('/',IndexPlatformController::class);
Router::addRouter('/favicon.ico',EmptyController::class);
Router::addRouter('/welcome',WelcomePlatformController::class);

//添加个人信息
Router::addRouter('/account/add',AccountController::class."@add");
//个人信息
Router::addRouter('/account/info/(\d+)',AccountController::class."@index#$1");
//更新账户
Router::addRouter('/account/update/(\d+)',AccountController::class."@update#$1");
//列表
Router::addRouter('/account',AccountController::class."@list");
Router::addRouter('/account/query',AccountController::class."@query");
//登录
Router::addRouter('/login',AuthController::class."@login");
Router::addRouter('/do_login',AuthController::class."@do_login");
Router::addRouter('/b2b/login',B2BAuthController::class."@login");
Router::addRouter('/b2b/do_login',B2BAuthController::class."@do_login");
//退出
Router::addRouter('/logout',AuthController::class."@logout");
//module router
Router::addModule("account","account");
Router::addModule("account_auth","account_auth");
Router::addModule("bus","bus");
Router::addModule("report","report");
Router::addModule("customer","customer");
Router::addModule("test","com\\oa\\test");

Application::getInstance()->dispatch($_SERVER['REQUEST_URI']);
