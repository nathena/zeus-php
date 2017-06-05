<?php

namespace zeus\http;
use zeus\utils\XssCleaner;

/**
 * Created by IntelliJ IDEA.
 * User: nathena
 * Date: 2017/6/2
 * Time: 20:31
 */

class XssWapperRequest extends Request
{
    public function __set($key,$val){
        $this->data[$key] = $this->xss($val);
    }

    private function xss($data){
        return XssCleaner::doClean($data);
    }
}