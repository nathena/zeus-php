<?php
namespace test_db;
/**
 * User: nathena
 * Date: 2017/6/13 0013
 * Time: 11:16
 */
use zeus\database\DbManager;
use zeus\database\specification\InsertSpecification;

include_once 'bootstrap.php';

$pdo = DbManager::openSession();

$pdo->beginTransaction();
try
{
    $fields = [
      'c_a'=>'1',
        'c_b'=>'2',
    ];
    $in = new InsertSpecification("t_test",$fields);
    $id = $pdo->execute($in);
    echo $id;

    $pdo->commit();
}
catch (\Exception $e)
{
    $pdo->rollBack();
    echo $e->getMessage();
}