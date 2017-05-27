<?php
namespace zeus\application;

abstract class AbstractCommand
{
	public function __construct()
	{
		$class = get_class($this);
		$classVal = explode("\\",$class);
		$simpleClassVal = end($classVal);
		
		$method_name = 'handler'.$simpleClassVal;
	
		$this->commandType = $class;
		$this->commandId = $this->commandType.time();
		
		$this->method = $method_name;
	}
	
	public function setResult($result){
	    $this->result = $result;
    }

    public function getResult(){
	    return $this->result;
    }

	public function register($commandHandler){
        if(!isset(static::$handlers[$this->commandType])){
            static::$handlers[$this->commandType] = [];
        }
        static::$handlers[$this->commandType][] = $commandHandler;
	}
	
	public function execute(){
		$this->start();
        $handlers = static::handlers[$this->commandType];
        foreach($handlers as $handler){
            if(!empty($handler) && class_exists($handler)){
                $this->handler(new $handler());
            }
        }
		$this->finished();
	}

	public function getData()
	{
		return $this->data;
	}

    public function __get($key){
        if(isset($this->data[$key])){
            return $this->data[$key];
        }
        return null;
    }
    public function __set($key,$val){
        $this->data[$key] = $val;
    }

    protected function start(){

    }

    protected function finished(){

    }

    private function handler($handler){
        if(is_object($handler) && method_exists($handler, $this->method)){
            $handler->{$this->method}($this);
        }else{
            throw new \RuntimeException(get_class($handler).':'.$this->method);
        }
    }

    private static $handlers = [];

    protected $data = [];
    protected $commandType;
    protected $commandId;
    protected $method;

    private $result;
}