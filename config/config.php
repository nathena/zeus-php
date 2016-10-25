<?php

$root = dirname(dirname(__FILE__));
$application = $root.DIRECTORY_SEPARATOR.'application';

return [
	
	'debug' => true,
		
	'default_timezone' => 'Asia/Shanghai',
	
	'xss_clean' => true,
		
	//REQUEST_URI、QUERY_STRING、PATH_INFO
	//'uri_protocol' => 'REQUEST_URI',
		
	'log_path' => $root.DIRECTORY_SEPARATOR.'logs',
	'log_level' => 0,
	
	'view' => [
		"template_path"			=> $application.DS.'views',
		'template_extension' 	=> ".htm",
		'template_lang_dir'		=> ''
	],	
		
	'app_ns' => [
		'app' => $application,
	],
];