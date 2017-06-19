<?php

$root = dirname(ZEUS_PATH);
$application = $root.DS.'application';

return [
	'debug' => true,
	'default_timezone' => 'Asia/Shanghai',
	//REQUEST_URI、QUERY_STRING、PATH_INFO
	'uri_protocol' => 'REQUEST_URI',

    //upload
    'upload_tmp_dir'=>'',
    'upload_max_filesize'=>'10M',

    //session
    'session.save_handler' => '',
    'session.var_session_id'=>'',
    'session.session_name'=>'zeus',
    'session.session_save_path'=>'',

    //application namespace
    'app_ns' => [
        'base' => $application.DS."base",
        'account'=> $application.DS."account",
        'account_auth'=> $application.DS."account_auth",
    ],

    //router
    'router.default_controller' => 'base\\IndexController',
    'router.default_controller_action' => 'Index',
    'router.rewrite' => [],

    //view
    'view.template_path' => $root.DS.'views',
    'view.template_extension' => ".html",
    'view.template_lang_dir' => "",

    //log
    'log.path' => $root.DS.'logs',
    'log.level' => 0,

    //database
    'database.pdo.dsn' => 'mysql:host=localhost;dbname=t_my',
    'database.pdo.user' => 'root',
    'database.pdo.pass' => '123456',
    'database.pdo.charset' => 'utf8',

];