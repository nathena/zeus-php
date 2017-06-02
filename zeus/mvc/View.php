<?php
namespace zeus\mvc;

use zeus\sandbox\ConfigManager;

class View
{
	private static $hook = [];
    private static $config;

    /**
     * Hook 方法注入
     * @access public
     * @param string|array  $method 方法名
     * @param mixed         $callback callable
     * @return void
     */
    public static function hook($method, $callback = null)
    {
        if (is_array($method))
        {
            self::$hook = array_merge(self::$hook, $method);
        }
        else
        {
            self::$hook[$method] = $callback;
        }
    }



    private $tpl_path;
	private $tpl_args = [];


    public function __construct($tpl_path)
    {
        if(!isset(self::$config)){
            self::$config = ConfigManager::config("view");
        }

        $this->template($tpl_path);
    }
    
    public function __call($method, $args)
    {
    	if (array_key_exists($method, self::$hook)) 
    	{
    		return call_user_func_array(self::$hook[$method], $args);
    	} 
    }
    

    public function assign($key,$val)
    {
    	$this->tpl_args[$key] = $val;
    }
    
    /**
     * @param $template
     * @return string
     */
    public function fetch()
    {
        ob_start();
        ob_implicit_flush(0);

    	if(is_file($this->tpl_path))
    	{
    		extract($this->tpl_args,EXTR_OVERWRITE);

    		include $this->tpl_path;
    	}

        return ob_get_clean();
    }

    public function display($content_type ="text/html",$code = 200){

        $response = Application::getInstance()->getResponse();

        $response->setCode($code)->setBody($this->fetch())->setHeader("Content-Typt",$content_type)->send();
    }

    //模板内部include
    private function template($template)
    {
        $this->tpl_path = realpath(self::$config['view.template_path'].DS.self::$config['view.template_lang_dir'].DS.$template.self::$config['view.template_extension']);
    }
}