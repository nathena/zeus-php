<?php
namespace zeus\http;

use zeus\Env;

class Request
{
	/**
	 * @var array 请求参数
	 */
	protected $get     = [];
	protected $post    = [];
	protected $put	   = [];
	protected $patch   = [];
	protected $delete  = [];
	protected $cookie  = [];
	protected $header  = [];
	protected $server  = [];
	
	protected $orgin_path = '';
	
	public function __construct()
	{
		$this->get = (isset($_GET)) ? $_GET : array();
		$this->post = (isset($_POST)) ? $_POST : array();
		$this->server = (isset($_SERVER)) ? $_SERVER : array();
		$this->env = (isset($_ENV)) ? $_ENV : array();
		$this->cookie = (isset($_COOKIE)) ? Cookie::get() : array();
		
		if (isset($_SERVER['REQUEST_METHOD'])) 
		{
			if ($this->isPut() || $this->isPatch() || $this->isDelete()) 
			{
				$this->parseData();
			}
		}
		
		// Get any possible request headers
		if (function_exists('getallheaders')) 
		{
			$this->headers = getallheaders();
		} 
		else 
		{
			foreach ($_SERVER as $key => $value) 
			{
				if (substr($key, 0, 5) == 'HTTP_') 
				{
					$key = ucfirst(strtolower(str_replace('HTTP_', '', $key)));
					if (strpos($key, '_') !== false) 
					{
						$ary = explode('_', $key);
						foreach ($ary as $k => $v){
							$ary[$k] = ucfirst(strtolower($v));
						}
						$key = implode('-', $ary);
					}
					$this->headers[$key] = $value;
				}
			}
		}
	}
	
	public function setOrginPath($uri_path='',$uri_protocol='REQUEST_URI')
	{
		$url_path = empty($uri_path) ? self::isCli()?$this->_parse_argv():$_SERVER[$uri_protocol] : $uri_path;
		
		$url_path = parse_url($url_path);
		if( isset($url_path["query"]))
		{
			parse_str($url_path["query"],$this->get);
		}
		
		$this->orgin_path = trim(strtolower($url_path["path"]),"/");
	}
	
	public function getOrginPath()
	{
		return $this->orgin_path;
	}
	
	public function get($key = '')
	{
		return empty($key) ? $this->get : isset($this->get[$key]) ? $this->get[$key] : '';
	}
	
	public function post($key = '')
	{
		return empty($key) ? $this->get : isset($this->post[$key]) ? $this->post[$key] : '';
	}
	
	public function put($key = '')
	{
		return empty($key) ? $this->put : isset($this->put[$key]) ? $this->put[$key] : '';
	}
	
	public function patch($key = '')
	{
		return empty($key) ? $this->patch : isset($this->patch[$key]) ? $this->patch[$key] : '';
	}
	
	public function delete($key = '')
	{
		return empty($key) ? $this->delete : isset($this->delete[$key]) ? $this->delete[$key] : '';
	}
	
	public function isAjax()
	{
		$value = $this->server('HTTP_X_REQUESTED_WITH');
		return (!is_null($value) && strtolower($value) == 'xmlhttprequest') ? true : false;
	}
	
	public function getMethod()
	{
		return $this->server['REQUEST_METHOD'];
	}
	
	/**
	 * Return whether or not the method is GET
	 *
	 * @return boolean
	 */
	public function isGet()
	{
		return ($this->server['REQUEST_METHOD'] == 'GET');
	}
	/**
	 * Return whether or not the method is HEAD
	 *
	 * @return boolean
	 */
	public function isHead()
	{
		return ($this->server['REQUEST_METHOD'] == 'HEAD');
	}
	/**
	 * Return whether or not the method is POST
	 *
	 * @return boolean
	 */
	public function isPost()
	{
		return ($this->server['REQUEST_METHOD'] == 'POST');
	}
	/**
	 * Return whether or not the method is PUT
	 *
	 * @return boolean
	 */
	public function isPut()
	{
		return ($this->server['REQUEST_METHOD'] == 'PUT');
	}
	/**
	 * Return whether or not the method is DELETE
	 *
	 * @return boolean
	 */
	public function isDelete()
	{
		return ($this->server['REQUEST_METHOD'] == 'DELETE');
	}
	/**
	 * Return whether or not the method is TRACE
	 *
	 * @return boolean
	 */
	public function isTrace()
	{
		return ($this->server['REQUEST_METHOD'] == 'TRACE');
	}
	/**
	 * Return whether or not the method is OPTIONS
	 *
	 * @return boolean
	 */
	public function isOptions()
	{
		return ($this->server['REQUEST_METHOD'] == 'OPTIONS');
	}
	/**
	 * Return whether or not the method is CONNECT
	 *
	 * @return boolean
	 */
	public function isConnect()
	{
		return ($this->server['REQUEST_METHOD'] == 'CONNECT');
	}
	/**
	 * Return whether or not the method is PATCH
	 *
	 * @return boolean
	 */
	public function isPatch()
	{
		return ($this->server['REQUEST_METHOD'] == 'PATCH');
	}
	
	protected function parseData()
	{
		$pData = file_get_contents('php://input');
		$paramData = array();
		
		if (isset($_SERVER['CONTENT_TYPE']) && (stripos($_SERVER['CONTENT_TYPE'], 'json') !== false)) 
		{
			// If the content-type is JSON
			$paramData = json_decode($pData, true);
		} 
		else if (isset($_SERVER['CONTENT_TYPE']) && (stripos($_SERVER['CONTENT_TYPE'], 'xml') !== false)) 
		{
			// Else, if the content-type is XML
			$matches = array();
			preg_match_all('/<!\[cdata\[(.*?)\]\]>/is', $pData, $matches);
			foreach ($matches[0] as $match) {
				$strip = str_replace(
						array('<![CDATA[', ']]>', '<', '>'),
						array('', '', '&lt;', '&gt;'),
						$match
						);
				$pData = str_replace($match, $strip, $pData);
			}
			$paramData = json_decode(json_encode((array) simplexml_load_string($pData)), true);
		} 
		else 
		{
			// Else, default to a regular URL-encoded string
			parse_str($pData, $paramData);
		}
		
		switch (strtoupper($this->getMethod())) 
		{
			case 'PUT':
				$this->put = $paramData;
				break;
			case 'PATCH':
				$this->patch = $paramData;
				break;
			case 'DELETE':
				$this->delete = $paramData;
				break;
		}
	}
	
	private function _parse_argv()
	{
		$args = array_slice($_SERVER['argv'], 1);
		return $args ? implode('/', $args) : '';
	}
		
	public static function isCli()
	{
		return stripos(PHP_SAPI, 'cli') === 0;
	}
	
	public static function isCgi()
	{
		return stripos(PHP_SAPI, 'cgi') === 0;
	}
	
	public static function ip($type = 0, $adv = false)
	{
		$type      = $type ? 1 : 0;
		
		static $ip = null;
		if (null !== $ip) 
		{
			return $ip[$type];
		}
		
		if ($adv) 
		{
			if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) 
			{
				$arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
				$pos = array_search('unknown', $arr);
				if (false !== $pos) 
				{
					unset($arr[$pos]);
				}
				$ip = trim($arr[0]);
			} 
			elseif (isset($_SERVER['HTTP_CLIENT_IP'])) 
			{
				$ip = $_SERVER['HTTP_CLIENT_IP'];
			} 
			elseif (isset($_SERVER['REMOTE_ADDR'])) 
			{
				$ip = $_SERVER['REMOTE_ADDR'];
			}
		} 
		elseif (isset($_SERVER['REMOTE_ADDR'])) 
		{
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		
		// IP地址合法验证
		$long = sprintf("%u", ip2long($ip));
		$ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
		
		return $ip[$type];
	}
}