<?php

$root = dirname(ZEUS_PATH);
$application = $root.DS.'application';

return [
	'default_timezone' => 'Asia/Shanghai',

    //upload
    'upload_tmp_dir'=>'',
    'upload_max_filesize'=>'10M',

    //session
    'session.save_handler' => '',
    'session.var_session_id'=>'',
    'session.session_name'=>'zeus',
    'session.session_save_path'=>'',

    //router
    'router.default_controller' => 'IndexController',
    'router.default_controller_action' => 'Index',

    //view
    'view.template_path' => $application.DS.'views',
    'view.template_extension' => ".htm",
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