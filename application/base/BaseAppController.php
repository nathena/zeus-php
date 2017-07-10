<?php
/**
 * User: nathena
 * Date: 2017/6/19 0019
 * Time: 10:06
 */

namespace base;


use zeus\mvc\Controller;

class BaseAppController extends Controller
{
    public function errorHandler(\Exception $e)
    {
        if($this->request->isAjax()){
            $this->response->setCode($e->getCode())->setBody($e->getMessage())->send("json");
        }else{
            $str = '<style>body {font-size:12px;}</style>';
            $str .= '<h1>操作失败！</h1><br />';
            $str .= '<strong>错误信息：<strong><font color="red">' . $e->getMessage() . '</font><br />';

            $this->response->setBody($str)->send();
        }
    }
}