<?php
namespace zeus\base\exception;

use RuntimeException;

class NestedException extends RuntimeException
{
	public function __construct ($message = null, $code = null, $previous = null)
	{
		parent::__construct($message,$code,$previous);
	}
}