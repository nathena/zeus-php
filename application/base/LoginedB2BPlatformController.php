<?php
/**
 * User: nathena
 * Date: 2017/6/19 0019
 * Time: 10:05
 */

namespace base;

use zeus\base\logger\Logger;
use zeus\http\Response;
use zeus\mvc\NeedCheckedInterface;
use zeus\sandbox\ApplicationContext;

class LoginedB2BPlatformController extends BaseAppController implements NeedCheckedInterface
{
    public function do_check()
    {
        $session = $this->request->getSession();
        //已经登录
        if(!isset($session['b2b_token'])){
            Logger::debug("=====".ApplicationContext::currentContext()->ip().$_SERVER['REQUEST_URI']." not login");
            Response::redirect("/b2b/login");

            return false;
        }

        return true;
    }
}