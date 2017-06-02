<?php
namespace zeus\mvc;

use zeus\base\exception\NestedException;

class ControllerNotFoundException extends NestedException
{
	public function __construct ($message = null, $code = null, $previous = null) 
	{
		parent::__construct($message,$code,$previous);
	}
}