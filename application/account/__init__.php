<?php
namespace account;

use zeus\mvc\Router;

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
//退出
Router::addRouter('/logout',AuthController::class."@logout");
