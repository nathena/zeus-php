<?php
namespace test_command;
/**
 * Created by IntelliJ IDEA.
 * User: nathena
 * Date: 2017/6/12
 * Time: 20:00
 */
use test\EchoCommand;

include_once 'bootstrap.php';

$command = new EchoCommand();
$command->execute();

$command->execute();
