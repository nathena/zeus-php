<?php 
ini_set("display_errors","On");
error_reporting(E_ALL);

define("APP_ENV_DIR", dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'config');

require_once '../zeus/start.php';