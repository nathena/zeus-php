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

        $this->response->setBody("12321")->send("json");
        //throw new \RuntimeException("异常测试");
    }

    public function test(){
        print_r($this->request->getData());
    }

    public function test2($a){
        print_r($this->request->getData());
        echo $a;
    }

    public function test_view()
    {
        echo 1;
        $view = $this->getView("test_view");
        $view->test = "2222";
        $view->abc = "2";
        $view->data = [1,2.3];

        //$view->display("application/json");
        $view->display();
    }
}