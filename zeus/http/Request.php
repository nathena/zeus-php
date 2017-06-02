<?php
namespace zeus\http;

use zeus\base\ApplicationContext;
use zeus\http\session\Session;

class Request
{
	private $data = [];
	private $headers = [];
	private $orgin_path = '';
	
	private $server;

	public function __construct($uri_protocol = 'REQUEST_URI')
	{
		$this->server = $_SERVER;
		if(ApplicationContext::isCli()){
			$args = array_slice($_SERVER['argv'], 1);
			$url_path = $args ? implode('/', $args) : '';
		}else{
			$url_path = $_SERVER[$uri_protocol];
		}
		$this->setOrginPath($url_path);
		$this->initRequest();
	}
	
	protected function initRequest()
	{
		if( $this->isGet()){
			$this->data = array_merge($this->data,$_GET); 
		}else if( $this->isPost() ){
			$this->data = array_merge($this->data,$_POST);
		}else if( $this->isPut() || $this->isPatch() || $this->isDelete() ){
			$this->data = array_merge($this->data,$this->parseData());
		}else{
			$this->data = array_merge($this->data,$_GET);
		}
	}
	
	public function __get($key){
		if(isset($this->data[$key])){
			return $this->data[$key];
		}
		return '';
	}
	
	public function __set($key,$val){
		$this->data[$key] = $val;
	}
	
	public function setData($data){
		$this->data = array_merge($this->data,$data);
	}
	
	public function getData(){
		return $this->data;
	}
	
	public function getHeader($key){
		$headers = getAllHeaders();
		if(isset($headers[$key])){
			return $headers[$key];
		}
		return '';
	}
	
	public function getAllHeaders(){
		
		if(!empty($this->headers)){
			return $this->headers;
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
	
	/**
	 * @return \zeus\http\Cookie
	 */
	public function getCookie(){
		return Cookie::getInstance();
	}
	
	/**
	 * @return \zeus\http\session\Session
	 */
	public function getSession(){
		return Session::getInstance();
	}
	
	public function getServer(){
		return $this->server;
	}
	
	public function getOrginPath(){
		return $this->orgin_path;
	}
	
	public function setOrginPath($orgin_path){
		if(!empty($orgin_path)){
			$url_path = parse_url($orgin_path);
			if( isset($url_path["query"]))
			{
				parse_str($url_path["query"],$data);
				$this->data = array_merge($this->data, $data);
			}
			$this->orgin_path = trim(strtolower($url_path["path"]),"/");
		}
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
	}
}