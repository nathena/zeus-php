<?php
/**
 * Created by IntelliJ IDEA.
 * User: nathena
 * Date: 2017/6/6 0006
 * Time: 10:00
 */

namespace test_router;

use zeus\mvc\Controller;

class IndexController extends Controller
{
    public function index(){
        echo __CLASS__,"=>",__METHOD__;
        print_r($this->getRequest()->getData());
    }

    public function test(){
        print_r($this->getRequest()->getData());
    }

    public function test2($a){
        print_r($this->getRequest()->getData());
        echo $a;
    }
}