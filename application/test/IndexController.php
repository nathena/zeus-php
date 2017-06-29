<?php
/**
 * User: nathena
 * Date: 2017/6/29 0029
 * Time: 11:17
 */

namespace com\oa\test;


use zeus\mvc\Controller;

class IndexController extends Controller
{
    public function index()
    {
        echo __NAMESPACE__,"<br />";
        echo __CLASS__,"<br />";
        echo $_SERVER['REQUEST_URI'],"<br />";
    }
}