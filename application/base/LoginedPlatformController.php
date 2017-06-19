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

class LoginedPlatformController extends BaseAppController implements NeedCheckedInterface
{
    public function do_check()
    {
        $session = $this->request->getSession();
        //已经登录
        if(!isset($session['token'])){
            Logger::debug("=====".ApplicationContext::currentContext()->ip().$_SERVER['REQUEST_URI']." not login");
            Response::redirect("/login");

            return false;
        }

        return true;
    }
}