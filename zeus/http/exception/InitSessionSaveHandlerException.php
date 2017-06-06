<?php
namespace zeus\http\exception;

use zeus\base\exception\NestedException;

class InitSessionSaveHandlerException extends NestedException
{
	public function __construct ($message = null, $code = null, $previous = null) 
	{
		parent::__construct($message,$code,$previous);
	}
}