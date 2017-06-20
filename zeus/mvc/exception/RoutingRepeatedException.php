<?php
namespace zeus\mvc\exception;

use zeus\base\exception\NestedException;

class RoutingRepeatedException extends NestedException
{
	public function __construct ($message = null, $code = null, $previous = null) 
	{
		parent::__construct($message,$code,$previous);
	}
}