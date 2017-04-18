<?php
namespace zeus\ddd\application;

use zeus\foundation\mvc\Application;

class Response
{
	public static $responseCodes = array(
			// Informational 1xx
			100 => 'Continue',
			101 => 'Switching Protocols',
			// Success 2xx
			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			203 => 'Non-Authoritative Information',
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',
			// Redirection 3xx
			300 => 'Multiple Choices',
			301 => 'Moved Permanently',
			302 => 'Found',
			303 => 'See Other',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			307 => 'Temporary Redirect',
			// Client Error 4xx
			400 => 'Bad Request',
			401 => 'Unauthorized',
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			406 => 'Not Acceptable',
			407 => 'Proxy Authentication Required',
			408 => 'Request Timeout',
			409 => 'Conflict',
			410 => 'Gone',
			411 => 'Length Required',
			412 => 'Precondition Failed',
			413 => 'Request Entity Too Large',
			414 => 'Request-URI Too Long',
			415 => 'Unsupported Media Type',
			416 => 'Requested Range Not Satisfiable',
			417 => 'Expectation Failed',
			// Server Error 5xx
			500 => 'Internal Server Error',
			501 => 'Not Implemented',
			502 => 'Bad Gateway',
			503 => 'Service Unavailable',
			504 => 'Gateway Timeout',
			505 => 'HTTP Version Not Supported',
			509 => 'Bandwidth Limit Exceeded'
	);
	
	protected $application;
	
	public static function create(Application $application)
	{
		return new self($application);
	}
	
	protected function __construct(Application $application)
	{
		$this->application = $application;
	}
	
	public function redirect($url, $code = '302', $version = '1.1')
	{
		if (headers_sent()) 
		{
			exit('<meta http-equiv="refresh" content="0; url='.$url.'" />');
		}
		else 
		{
			header("HTTP/{$version} {$code} " . self::$responseCodes[$code]);
			header("Location: {$url}");
		}
	}
	
	public function forward($url)
	{
		$this->application->start($url);
	}
}