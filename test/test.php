<?php
namespace test;

use zeus\database\specification\DeleteSpecification;
use zeus\database\specification\InsertBatchSpecification;
use zeus\database\specification\InsertSpecification;
use zeus\database\specification\QueryRowSpecification;
use zeus\database\specification\QuerySpecification;
use zeus\database\specification\UpdateSpecification;

include_once 'bootstrap.php';

$q = new QueryRowSpecification();
$q->select("*")->from("t_test")->where("id",1);
$c = new QuerySpecification();
$c->where("b > ",2);
$c->where("d < ",3);
$q->or_where($c);
$q->log();
echo $q->getDml();

$q = new InsertSpecification("t_est",["a"=>1,"b"=>2,"c"=>3]);
$q->log();
echo $q->getDml();

$data = [];
$params = ["a"=>11,"b"=>22,"c"=>33];
$data[] = $params;
$params = ["a"=>"aa","b"=>"bb","c"=>"cc"];
$data[] = $params;
$q = new InsertBatchSpecification("t_est",$data);
$q->log();
echo $q->getDml();

$q = new UpdateSpecification("t_test",["a"=>1,"b"=>2]);
$q->where("id",1)->log();
echo $q->getDml();

$q = new DeleteSpecification("t_tese");
$q->where("id",2)->log();
echo $q->getDml();
