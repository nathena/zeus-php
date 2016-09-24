<?php
namespace zeus\exception;

class ClassNotFoundException extends \RuntimeException
{
	public function __construct ($message = null, $code = null, $previous = null) 
	{
		parent::__construct($message,$code,$previous);
	}
}