<?php
/**
 * User: nathena
 * Date: 2017/6/29 0029
 * Time: 11:17
 */

namespace test;


use base\BaseAppController;
use zeus\utils\Il8n;

class IndexController extends BaseAppController
{
    public function index()
    {
        echo __NAMESPACE__,"<br />";
        echo __CLASS__,"<br />";
        echo $_SERVER['REQUEST_URI'],"<br />";

        echo Il8n::get("您好，%s！","zzzzz","zzzzz111","zzzzz111","zzzzz111");
    }
}