<?php
/**
 * User: nathena
 * Date: 2017/7/10 0010
 * Time: 14:40
 */

namespace account;


use base\BaseAppController;
use zeus\http\Response;

class LogoutController extends BaseAppController
{
    public function index()
    {
        $session = $this->request->getSession();
        $session->kill();

        Response::redirect("/");
    }
}