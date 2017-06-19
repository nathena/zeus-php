<?php
namespace test_bus;


use zeus\domain\AbstractEntity;

include_once "bootstrap.php";


class User extends AbstractEntity
{
    protected $schema = "t_user";
}

$user = new User();
$user->setProperties(["a"=>'1','b'=>'2',"id"=>3]);
$user->setData(["a"=>'a','b'=>'b',"id"=>2]);

//var_dump($user);
var_dump($user->getData());
var_dump($user->getProperties());
var_dump($user->getSchema());
var_dump($user->getId());