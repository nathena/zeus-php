<?php
namespace zeus\ddd\exception;

class InvalidArgumentException extends NestedException
{
	public function __construct ($message = null, $code = null, $previous = null) 
	{
		parent::__construct($message,$code,$previous);
	}
}