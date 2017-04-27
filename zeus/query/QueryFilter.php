<?php
namespace zeus\query;

abstract class QueryFilter
{
	protected $queryType;
	protected $queryId;
	protected $method;
	
	protected $queryHandler;
	
	public function __construct()
	{
		$class = get_class($this);
		$classVal = explode("\\",$class);
		$simpleClassVal = end($classVal);
		
		$method_name = 'handler'.$simpleClassVal;
	
		$this->queryType = $class;
		$this->queryId = $this->queryType.time();
		$this->method = $method_name;
	}
	
	public function getQueryType()
	{
		return $this->queryType;
	}
	
	public function getId()
	{
		return $this->queryId;
	}
	
	public function query(){
		if(is_object($this->queryHandler) && method_exists($this->handler, $this->method)){
			return $this->queryHandler->{$this->method}($this);
		}
		return null;
	}
}