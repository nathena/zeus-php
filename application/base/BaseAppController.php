<?php
/**
 * User: nathena
 * Date: 2017/6/19 0019
 * Time: 10:06
 */

namespace base;


use zeus\mvc\Controller;
use zeus\sandbox\ConfigManager;

class BaseAppController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        $config = ConfigManager::getInstance();
        $config['il8n.config.path'] = __APPLICATION__.DS.'config'.DS.'chinese.ini';
    }

    public function errorHandler(\Exception $e)
    {
        $this->modelMap['err_code'] = $e->getCode();
        $this->modelMap['err_message']  =  $e->getMessage();
        $this->modelMap['err']  =  $e;

        if($this->request->isAjax()){
            return "json:";

        }

        return "error";
    }
}