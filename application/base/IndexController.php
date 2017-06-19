<?php
/**
 * User: nathena
 * Date: 2017/6/19 0019
 * Time: 9:57
 */

namespace base;


class IndexController extends MustLoginedController
{
    public function index(){
        echo __CLASS__,'=>',__METHOD__;
    }
}