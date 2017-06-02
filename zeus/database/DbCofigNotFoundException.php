<?php
namespace zeus\database;

use zeus\base\exception\NestedException;

class DbCofigNotFoundException extends NestedException
{
	public function __construct ($message = null, $code = null, $previous = null) 
	{
		parent::__construct($message,$code,$previous);
	}
}