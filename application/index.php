<?php
/**
 * Created by IntelliJ IDEA.
 * User: nathena
 * Date: 15/8/5
 * Time: 10:25
 */
include_once "../zeus/Zeus.php";

use zeus\Wx;

Zeus::accept(dirname(__FILE__));
//session_set_cookie_params()

echo \zeus\HttpReq::get(array("url"=>"http://www.baidu.com"));