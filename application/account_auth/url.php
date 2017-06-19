<?php
//account_auth 依赖account模块
return [
    //登录
    '/login' => \account_auth\AuthController::class."@login",
    '/do_login' => \account_auth\AuthController::class."@do_login",
    //退出
    '/logout' => \account_auth\AuthController::class."@logout",
];
