<?php
/**
 * User: nathena
 * Date: 2017/6/29 0029
 * Time: 11:42
 */

namespace base;


use zeus\mvc\Controller;

class EchoController extends Controller
{
    public function index()
    {
        echo __NAMESPACE__,"<br />";
        echo __CLASS__,"<br />";
        echo $_SERVER['REQUEST_URI'],"<br />";

        echo print_r(func_get_args());
    }
}