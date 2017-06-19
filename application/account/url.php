<?php
return [
    //添加个人信息
    '/account/add' => '',
    //个人信息
    '/account/info/(\d+)' => '',
    //更新账户
    '/account/update/(\d+)' => '',
    //列表
    '/account' => '',

    //登录
    '/login' => \account\AuthController::class."@login",
    '/do_login' => \account\AuthController::class."@do_login",
    //退出
    '/logout' => \account\AuthController::class."@logout",
];
