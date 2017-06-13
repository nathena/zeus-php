<?php
namespace test_view;
/**
 * User: nathena
 * Date: 2017/6/13 0013
 * Time: 11:31
 */
use zeus\mvc\Application;

include_once 'bootstrap.php';

Application::getInstance()->dispatch("test/view");