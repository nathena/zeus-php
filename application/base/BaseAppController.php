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
    public function beforeAction()
    {
        parent::beforeAction();

        if( $this->request->getMethod() == 'POST' && !$this->check_csrf_token())
        {
            throw new \RuntimeException("非法的提交请求");
        }
    }

    public function errorHandler(\Exception $e)
    {
        if($this->request->isAjax()){
            $this->response->setBody($e->getMessage())->send("json");
        }else{
            $str = '<style>body {font-size:12px;}</style>';
            $str .= '<h1>操作失败！</h1><br />';
            $str .= '<strong>错误信息：<strong><font color="red">' . $e->getMessage() . '</font><br />';

            $this->response->setBody($str)->send();
        }
    }
}