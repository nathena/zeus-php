<?php
/**
 * Created by IntelliJ IDEA.
 * User: nathena
 * Date: 2017/6/6 0006
 * Time: 10:45
 */

namespace app;


use zeus\mvc\Controller;

class IndexController extends Controller
{
    public function index($data=null){
        echo __CLASS__,"=>",__METHOD__;
        print_r($this->getRequest()->getData());
        print_r($data);
    }
}