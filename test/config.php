<?php

$root = dirname(dirname(__FILE__));
$application = $root.DS.'application';

return [

	//REQUEST_URI、QUERY_STRING、PATH_INFO
	'uri_protocol' => 'REQUEST_URI',
	'xss_clean' => true,

    //application namespace
    'app_ns' => [
        'app' => $application,
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