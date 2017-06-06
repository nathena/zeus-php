<?php

$current_dir = dirname(__FILE__);
$root = dirname($current_dir);

return [

	//REQUEST_URI、QUERY_STRING、PATH_INFO
	'uri_protocol' => 'REQUEST_URI',
	'xss_clean' => true,

    //application namespace
    'app_ns' => [
        'test_router' => $current_dir.DS."test_router",
        'app' => $current_dir.DS."app",
    ],

    //log
    'log.path' => $root.DS.'logs',
    'log.level' => 0,

    //database
    'database.dsn' => 'mysql:host=localhost;dbname=t_my',
    'database.user' => 'root',
    'database.pass' => '123456',
    'database.charset' => 'utf8',
];