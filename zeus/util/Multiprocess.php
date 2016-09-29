<?php
namespace zeus\util;

use zeus\logger\Logger;

/**
 * 
 * 多进程
 */
class Multiprocess
{
	private $host; //主机
	private $server; //主机
	private $port; //端口
	
	private $errno = 0;
	private $errstr = '';
	private $timeout = 10;
	
	private $processName = '';
	private $runable;

	private static $process = 1;
	
	private static $boundary = 'abcdefghijklmnopqrstuvwxyz1234567890';
	
	public static $listener = array();

	private function __construct()
	{
		$this->processName = 'Process-'.(self::$process++);

		$this->server = $_SERVER['SERVER_NAME'];
		$this->host = $_SERVER['HTTP_HOST'];
		$this->port = $_SERVER['SERVER_PORT'];

		$this->request = '/?'.self::$boundary;
	}
	
	public final function getName()
	{
		return $this->processName;
	}
	
	public final function getErrno()
	{
		return $this->errno;
	}
	
	public final function getErrstr()
	{
		return $this->errstr;
	}
	
	public final function getTimeout()
	{
		return $this->timeout;
	}

	private function run($data)
	{
		if (empty($data))
			return;
		
		$post = http_build_query($data);
		
		$req = "POST " . $this->request . " HTTP/1.1 \r\n";
		$req .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$req .= "Content-Length: " . strlen($post) . "\r\n";
		$content = $post . "\r\n\r\n";
		
		$this->host && ( $boardurl = "http://" . $this->host . "/" );
		
		$req .= "Host: " . $this->host . "\r\n";
		$req .= "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\n";
		$req .= "Accept-Language: zh-cn\r\n";
		isset($boardurl) && $boardurl && ( $req .= "Referer: $boardurl\r\n" );
		$req .= "User-Agent: {$_SERVER['HTTP_USER_AGENT']}\r\n";
		$req .= "Accept-Encoding: gzip,deflate\r\n";
		$req .= "Accept-Charset: GB2312,utf-8;q=0.7,*;q=0.7\r\n";
		$req .= "Cookie: {$_SERVER['HTTP_COOKIE']}\r\n";
		$req .= "Connection: close\r\n\r\n";
		$req .= $content;
		
		$sock = fsockopen($this->host, $this->port, $this->errno, $this->errstr, $this->timeout);
		
		if ($sock)
		{
			stream_set_blocking($sock, 0);
			fwrite($sock, $req);
			fflush($sock);
			usleep(5);
			fclose($sock);
		}
	}

	public final static function exec($data)
	{
		$process = new self();
		$process->run($data);
		
		Logger::info( ' === '.$process->getName().' ==== started === ' );
		
		return $process;
	}
	
	public final static function listener()
	{
		if (isset($_GET[self::$boundary]) && ip() == gethostbyname($_SERVER['HTTP_HOST']))
		{
			Logger::info( ' === '.__CLASS__.' '.__METHOD__.' === ');
			
			foreach( self::$listener as $key => $val )
			{
				$process = new $val();
				
				if( $process instanceof MultiprocessHandler )
				{
					$process->run();
				}
			}
			
			exit;
		}
	}
}