<?php
namespace zeus\ddd\application;

use zeus\foundation\mvc\Application;
use zeus\ddd\application\filter\FilterInterface;

class Request
{
	/**
	 * @var array 请求参数
	 */
	private $get     = [];
	private $post    = [];
	private $put	   = [];
	private $patch   = [];
	private $delete  = [];
	private $cookie  = [];
	private $header  = [];
	private $server  = [];
	
	private $params  =[];
	private $_request  =[];
	private $orgin_path;
	
	protected $application;
	
	public static function create(Application $application)
	{
		return new self($application);
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
	
	protected function __construct(Application $application)
	{
		$this->application = $application;
		
		$this->init();
	}
	
	protected function init()
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
		
		$this->_request = array_merge($this->_request, $this->get);
		$this->_request = array_merge($this->_request, $this->post);
		$this->_request = array_merge($this->_request, $this->put);
		$this->_request = array_merge($this->_request, $this->patch);
		$this->_request = array_merge($this->_request, $this->delete);
		
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
	
	public function getOrginPath($uri_protocol = 'REQUEST_URI')
	{
		if(empty($uri_protocol)){
			$uri_protocol = 'REQUEST_URI';
		}
		
		if( empty($this->orgin_path) )
		{
			$url_path = self::isCli()?$this->cliOrginPath():$_SERVER[$uri_protocol];
			$url_path = parse_url($url_path);
			if( isset($url_path["query"]))
			{
				parse_str($url_path["query"],$this->get);
				
				$this->params = array_merge($this->params, $this->get);
			}
			
			$this->orgin_path = trim(strtolower($url_path["path"]),"/");
		}
		return $this->orgin_path;
	}
	
	public function params($key = '')
	{
		return $this->data('params',$key);
	}
	
	public function get($key = '')
	{
		return $this->data('get',$key);
	}
	
	public function post($key = '')
	{
		return $this->data('post',$key);
	}
	
	public function put($key = '')
	{
		return $this->data('put',$key);
	}
	
	public function patch($key = '')
	{
		return $this->data('patch',$key);
	}
	
	public function delete($key = '')
	{
		return $this->data('delete',$key);
	}
	
	public function cookie($key='')
	{
		return $this->data('cookie',$key);
	}
	
	public function _request($key = '')
	{
		return $this->data('_request',$key);
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
	
	public function doFilter(FilterInterface $filter)
	{
		$this->get( $filter->doFilter($this->get()) );
		$this->post( $filter->doFilter($this->post()) );
		$this->put( $filter->doFilter($this->put()) );
		$this->patch( $filter->doFilter($this->patch()) );
		$this->delete( $filter->doFilter($this->delete()) );
		$this->cookie( $filter->doFilter($this->cookie()) );
		$this->params( $filter->doFilter($this->params()) );
		
		$this->_request( $filter->doFilter($this->_request()) );
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
	
	public function cliOrginPath()
	{
		$args = array_slice($_SERVER['argv'], 1);
		return $args ? implode('/', $args) : '';
	}
	
	private function data($data, $key='')
	{
		if( !property_exists($this, $data) )
		{
			return null;
		}
	
		if( empty($key) )
		{
			return $this->{$data};
		}
	
		if( is_array($key) )
		{
			foreach($key as $k => $v)
			{
				$this->{$data}[$k] = $v;
			}
				
			return $this->{$data};
		}
	
		return isset($this->{$data}[$key]) ? $this->{$data}[$key] : '';
	
	}
}