<?php
/**
 * User: nathena
 * Date: 2017/6/19 0019
 * Time: 9:57
 */

namespace base;


class IndexPlatformController extends LoginedPlatformController
{
    public function index(){
        echo __CLASS__,'=>',__METHOD__;
    }
}