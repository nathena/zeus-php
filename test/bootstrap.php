<?php
/**
 * Created by IntelliJ IDEA.
 * User: nathena
 * Date: 2017/6/5 0005
 * Time: 17:04
 */
$path = __FILE__;
$zeus_path = dirname(__DIR__).DIRECTORY_SEPARATOR."zeus".DIRECTORY_SEPARATOR."bootstrap.php";

include_once $zeus_path;

print_r(\zeus\sandbox\ConfigManager::config());

\zeus\base\logger\Logger::error("1212121");
\zeus\base\logger\Logger::warn("阿发发呆发呆发呆");