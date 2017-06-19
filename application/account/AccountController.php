<?php
/**
 * User: nathena
 * Date: 2017/6/19 0019
 * Time: 10:04
 */

namespace account;


use zeus\mvc\Controller;

class AccountController extends Controller
{
    public function index()
    {
        echo __CLASS__,"=>",__METHOD__;
    }
}