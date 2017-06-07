<?php
namespace test;
use zeus\database\specification\InsertSpecification;
use zeus\database\specification\QuerySpecification;

$current_dir = dirname(__FILE__);
$root = dirname($current_dir);

define("APP_ENV_PATH",$current_dir.DIRECTORY_SEPARATOR."config.php");
$zeus_path = $root.DIRECTORY_SEPARATOR."zeus".DIRECTORY_SEPARATOR."bootstrap.php";
include_once $zeus_path;


//$spec = new QuerySpecification();
//$spec->select("a1,a2,a3")->from("test a")->join("test_b b","a.id = b.id")
//       ->where("a.id",1);
//$spec->where_in("a.c",[1,2,3.1,2,3,1,2,3,1]);
//
//
//$other = new QuerySpecification();
//$other->where_in("a.d",["a"]);
//$other->where("aaa > ",1);
//$spec->or_where($other);
//
////$spec->like("a.c not","aaa");
////echo $spec->getSql();
////print_r($spec->getParams());
//echo $spec->test();
echo "\r\n===>";

$spec = new InsertSpecification("caadfa");
$spec->insert(["a"=>'b',"c"=>1]);
echo $spec->test();
