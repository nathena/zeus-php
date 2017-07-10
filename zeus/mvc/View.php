<?php

namespace zeus\mvc;

use zeus\sandbox\ConfigManager;

class View implements \ArrayAccess
{
    private static $hook = [];

    private $tpl_path;
    private $tpl_args = [];

    private $code;
    private $content_type;

    /**
     * Hook 方法注入
     * @access public
     * @param string|array $method 方法名
     * @param mixed $callback callable
     * @return void
     */
    public static function hook($method, $callback = null)
    {
        if (is_array($method)) {
            self::$hook = array_merge(self::$hook, $method);
        } else {
            self::$hook[$method] = $callback;
        }
    }


    public function __construct($tpl_path,$code = 200,$content_type = "text/html")
    {
        $this->code = 200;
        $this->content_type = $content_type;
        $this->template($tpl_path);
    }

    public function __call($method, $args)
    {
        if (array_key_exists($method, self::$hook)) {
            return call_user_func_array(self::$hook[$method], $args);
        }
    }

    public function __get($key)
    {
        if (isset($this->tpl_args[$key])) {
            return $this->tpl_args[$key];
        }
        return null;
    }

    public function __set($key, $val)
    {
        $this->tpl_args[$key] = $val;
    }

    public function offsetExists($offset)
    {
        return isset($this->{$offset});
    }

    public function offsetGet($offset)
    {
        return $this->{$offset};
    }

    public function offsetSet($offset, $value)
    {
        $this->{$offset} = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->{$offset});
    }


    /**
     * @param $template
     * @return string
     */
    public function fetch()
    {
        if (is_file($this->tpl_path)) {
            extract($this->tpl_args, EXTR_OVERWRITE);

            include $this->tpl_path;
        }
        $content = ob_get_contents();
        ob_clean();
        return $content;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getContentType()
    {
        return $this->content_type;
    }

    //模板内部include
    protected function template($template)
    {
        $this->tpl_path = realpath(ConfigManager::config('view.template_path') . DS . ConfigManager::config('view.template_lang_dir') . DS . $template . ConfigManager::config('view.template_extension'));
    }
}