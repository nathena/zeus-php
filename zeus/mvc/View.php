<?php
namespace zeus\mvc;

use zeus\sandbox\ConfigManager;

class View
{
	protected static $config = [
			"template_path"			=> '',
			'template_extension' 	=> ".htm",
			'template_lang_dir'		=> ''
	];
	
	private static $hook = [];
	
	private $tpl_args = [];
	
	
	/**
     * Constructor
     *
     * @access   publicssss
     */
    public function __construct(array $config = [])
    {
    	if (empty($config)) {
    		$config = ConfigManager::config('view');
    	}
        self::$config = array_merge(self::$config,array_change_key_case($config));
    }
    
    public function __call($method, $args)
    {
    	if (array_key_exists($method, self::$hook)) 
    	{
    		array_unshift($args, $this);
    		
    		return call_user_func_array(self::$hook[$method], $args);
    	} 
    }
    
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
      
    public function assign($key,$val="")
    {
    	$this->tpl_args[$key] = $val;
    }
    
    //模板内部include
    public function tpl($template)
    {
    	return realpath(self::$config['template_path'].DIRECTORY_SEPARATOR.self::$config['template_lang_dir'].DIRECTORY_SEPARATOR.$template.self::$config['template_extension']);
    }
    
    /**
     * executes & displays the template results
     *
     * @param string $resource_name
     * @param string $cache_id
     * @param string $compile_id
     */
    public function display($template,$header = '')
    {
    	if( empty($header) )
    	{
    		$header = array('Content-Type' => 'text/html');
    	}
    	
    	if(!empty($header) && is_array($header))
    	{
    		foreach ($header as $key => $val)
    		{
    			header("$key:$val");
    		}	
    	}
    	
        $this->fetch($template,true); 
    }

    /**
     * executes & returns or displays the template results
     *
     * @param string $resource_name
     * @param string $cache_id
     * @param string $compile_id
     * @param boolean $display
     */
    public function fetch($template,$display=false)
    {
    	if( $view_tpl = $this->tpl($template) )
    	{
    		ob_start();
    		ob_implicit_flush(0);
    		
    		extract($this->tpl_args,EXTR_OVERWRITE);
    		
    		include $view_tpl;
    		
    		if( $display )
    		{
    			return ob_end_flush();
    		}
    		return ob_get_clean();
    	}
    	
    	throw new \Exception("template $template => $view_tpl not found");
    }
}