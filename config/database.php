<?php
return [
		'type' => 'pdo',
		'pdo' => [
				'dsn'            =>'mysql:host=localhost;dbname=t_my',
				'user'           => 'root',
				'pass' 	         => '123456',
				'driver_options' => [
						\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
						\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
				],
		],
];