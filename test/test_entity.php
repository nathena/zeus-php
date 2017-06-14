<?php
namespace test_bus;


use zeus\domain\AbstractEntity;

include_once "bootstrap.php";


class User extends AbstractEntity
{
    public function __construct($data)
    {
        parent::__construct($data);
    }
}

$user = new User(['id'=>222]);
$user->setData(["a"=>'a','b'=>'b',"id"=>2]);

//var_dump($user);
var_dump($user->getData());
var_dump($user->getUpdatedData());