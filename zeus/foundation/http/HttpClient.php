<?php
namespace zeus\foundation\http;

class HttpClient
{
	public static function get($query)
	{
		$url = $query["url"];
		$reqheaders = $query["reqheaders"];
		$response = self::_query("GET",$url,null,$reqheaders);
		return $response;
	}
	public static function delete($query)
	{
		$url = $query["url"];
		$reqheaders = $query["reqheaders"];
		$response = self::_query("DELETE",$url,null,$reqheaders);
		return $response;
	}
	public static function post($query)
	{
		$url = $query["url"];
		$data = is_array($query["data"]) ? http_build_query($query["data"]) : null;
		$reqheaders = $query["reqheaders"];
		$response = self::_query("POST",$url,$data,$reqheaders);
		return $response;
	}
	public static function put($query)
	{
		$url = $query["url"];
		$data = is_array($query["data"]) ? http_build_query($query["data"]) : null;
		$reqheaders = $query["reqheaders"];
		$response = self::_query("PUT",$url,$data,$reqheaders);
		return $response;
	}
	public static function postFile($query)
	{
		$url = $query["url"];
		$data = $query["data"];
		$files = $data["__uploadfiles__"];
		$reqheaders = is_array($query["reqheaders"])?$query["reqheaders"]:array();
		if( !is_array($files) )
		{
			unset($data["__uploadfiles__"]);
			return self::post($query);
		}
		$multipart_boundary = '--------------------------'.microtime(true);
		$reqheaders[] = 'Content-Type: multipart/form-data; boundary='.$multipart_boundary;
		$content = self::createMultpartFileData($multipart_boundary,$files);
		// add some POST fields to the request too: $_POST['foo'] = 'bar'
		unset($data["__uploadfiles__"]);
		foreach($data as $key => $val )
		{
			$content .= "--".$multipart_boundary."\r\n".
					"Content-Disposition: form-data; name=\"".rawurlencode($key)."\"\r\n\r\n".
					rawurlencode($val)."\r\n";
		}
		// signal end of request (note the trailing "--")
		$content .= "--".$multipart_boundary."--\r\n";
		$header = implode("\r\n",$reqheaders);
		$context = stream_context_create(array(
				'http' => array(
						'method' => 'POST',
						'header' => $header,
						'content' => $content,
				)
		));
		return file_get_contents($url, false, $context);
	}
	public static function getFile($url,$outfile)
	{
		$response = self::_query("GET",$url);
		file_put_contents($outfile,$response);
	}
	private static function _query($method,$url,$data=null,$reqheaders=null,$timeout=20)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		if(!empty($reqheaders) && is_array($reqheaders))
		{
			curl_setopt($ch, CURLOPT_HTTPHEADER, $reqheaders);
		}
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		switch($method)
		{
			case "GET" :
				curl_setopt($ch, CURLOPT_HTTPGET, true);
				break;
			case "POST":
				curl_setopt($ch, CURLOPT_POST,true);
				curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
				break;
			case "PUT" :
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
				curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
				break;
			case "DELETE":
				curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
				break;
			default :
				curl_setopt($ch, CURLOPT_HTTPGET, true);
				break;
		}
		$response = curl_exec($ch);//获得返回值
		curl_close($ch);
		return $response;
	}
	private static function createMultpartFileData($multipart_boundary,$files)
	{
		$content = "";
		foreach($files as $key => $_fileParam)
		{
			if( is_array($_fileParam))
			{
				foreach( $_fileParam as $_file )
				{
					if( $_file instanceof \CURLFile )
					{
						$content.="--".$multipart_boundary."\r\n".
								"Content-Disposition: form-data; name=\"".$key."\"; filename=\"".$_file->getPostFilename()."\"\r\n".
								"Content-Type: ".$_file->getMimeType()."\r\n\r\n".
								file_get_contents($_file->getFilename())."\r\n";
					}
					else if( $_filepath = realpath($_file) )
					{
						$content.="--".$multipart_boundary."\r\n".
								"Content-Disposition: form-data; name=\"".$key."\"; filename=\"".basename($_filepath)."\"\r\n".
								"Content-Type: ".mime_content_type($_filepath)."\r\n\r\n".
								file_get_contents($_filepath)."\r\n";
					}
				}
			}
			else if( $_fileParam instanceof \CURLFile )
			{
				$content.="--".$multipart_boundary."\r\n".
						"Content-Disposition: form-data; name=\"".$key."\"; filename=\"".$_fileParam->getPostFilename()."\"\r\n".
						"Content-Type: ".$_fileParam->getMimeType()."\r\n\r\n".
						file_get_contents($_fileParam->getFilename())."\r\n";
			}
			else if( $_filepath = realpath($_fileParam) )
			{
				$content.="--".$multipart_boundary."\r\n".
						"Content-Disposition: form-data; name=\"".$key."\"; filename=\"".basename($_filepath)."\"\r\n".
						"Content-Type: ".mime_content_type($_filepath)."\r\n\r\n".
						file_get_contents($_filepath)."\r\n";
			}
		}
		return $content;
	}
}