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
        print_r($this->request->getData());

        echo $this->request->a;

        echo intval($this->check_csrf_token());

        //throw new \RuntimeException("异常测试");
    }

    public function test(){
        print_r($this->request->getData());
    }

    public function test2($a){
        print_r($this->request->getData());
        echo $a;
    }
}