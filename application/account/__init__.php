<?php
return [
    //添加个人信息
    '/account/add' => \account\AccountController::class."@add",
    //个人信息
    '/account/info/(\d+)' => \account\AccountController::class,"@index#$1",
    //更新账户
    '/account/update/(\d+)' => \account\AccountController::class."@update#$1",
    //列表
    '/account' => \account\AccountController::class."@list",
    '/account/query' => \account\AccountController::class."@query",

    //登录
    '/login' => \account\AuthController::class."@login",
    '/do_login' => \account\AuthController::class."@do_login",
    //退出
    '/logout' => \account\AuthController::class."@logout",
];
