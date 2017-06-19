<?php
/**
 * User: nathena
 * Date: 2017/6/19 0019
 * Time: 10:09
 */

namespace account_auth;


use account\domain\Account;
use base\BaseAppController;
use zeus\database\specification\QueryRowSpecification;
use zeus\domain\DbRepository;
use zeus\http\Response;

class AuthController extends BaseAppController
{
    public function login()
    {
        $session = $this->request->getSession();
        //已经登录
        if(isset($session['token'])){
            Response::redirect("/");
            return;
        }

        $view = $this->getView("auth/login");
        $view->display();
    }

    public function do_login()
    {
        $account = $this->request['account'];
        $passwd  = $this->request['passwd'];
        $follow  = $this->request['follow'];
        if(empty($follow)){
            $follow = "/";
        }

        if(empty($account)){
            throw new \RuntimeException("登录账户不能为空");
        }

        if(empty($passwd)){
            throw new \RuntimeException("登录账户密码不能为空");
        }

        //TODO
        $spect = new QueryRowSpecification();
        $spect->from("t_account")->where("user_name",trim($account));

        $account = DbRepository::getSchema("t_account")->load(Account::class,$spect);

        if(empty($account)){
            throw new \RuntimeException("登录账户不存在");
        }

        if(!$account->check_passwd($passwd))
        {
            throw new \RuntimeException("登录账户密码不存在");
        }

        $session = $this->request->getSession();
        $session['token'] = $account['id'];

        Response::redirect($follow);
    }

    public function logout()
    {
        $session = $this->request->getSession();
        $session->kill();

        Response::redirect("/");
    }
}