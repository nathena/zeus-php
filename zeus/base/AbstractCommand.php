<?php
namespace zeus\base;

abstract class AbstractCommand extends AbstractMessage
{
	public function __construct()
	{
		$class = get_class($this);
		$classVal = explode("\\",$class);
		$simpleClassVal = end($classVal);
		$method_name = 'handler'.$simpleClassVal;
	
		$this->msgType = $class;
		$this->msgId = $this->msgType.time();
		$this->method = $method_name;
	}
}