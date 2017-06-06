<?php
/**
 * Created by IntelliJ IDEA.
 * User: nathena
 * Date: 2017/6/5 0005
 * Time: 17:04
 */

$current_dir = dirname(__FILE__);
$root = dirname($current_dir);

define("APP_ENV_PATH",$current_dir.DIRECTORY_SEPARATOR."config.php");
$zeus_path = $root.DIRECTORY_SEPARATOR."zeus".DIRECTORY_SEPARATOR."bootstrap.php";

include_once $zeus_path;

print_r(\zeus\sandbox\ConfigManager::config());
\zeus\mvc\Application::getInstance()->dispatch("/test?a='''12&b=2");