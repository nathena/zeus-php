<?php

namespace zeus\http;

use zeus\sandbox\ApplicationContext;

class Request implements \ArrayAccess
{
    protected $data = [];
    protected $headers = [];
    protected $server;

    public function __construct()
    {
        $this->server = $_SERVER;
        $this->getAllHeaders();

        if ($this->isPost()) {
            $this->setData($_POST);
        } else if ($this->isPut() || $this->isPatch() || $this->isDelete()) {
            $this->setData($this->parseData());
        } else {
            $this->setData($_GET);
        }
    }

    public function __get($key)
    {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }
        return '';
    }

    public function __set($key, $val)
    {
        $this->data[$key] = $val;
    }

    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    public function __unset($name)
    {
        unset($this->data[$name]);
    }

    public function offsetExists($offset)
    {
        return isset($this->{$offset});
    }

    public function offsetGet($offset)
    {
        return $this->{$offset};
    }

    public function offsetSet($offset, $value)
    {
        $this->{$offset} = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->{$offset});
    }

    public function setData($data)
    {
        foreach ($data as $key => $val) {
            $this->{$key} = $val;
        }
    }

    public function getData()
    {
        return $this->data;
    }

    public function getHeader($key)
    {
        if (isset($this->headers[$key])) {
            return $this->headers[$key];
        }
        return '';
    }

    public function getServer()
    {
        return $this->server;
    }

    public function getCookie()
    {
        return new Cookie($this);
    }

    public function getSession()
    {
        return Session::getInstance();
    }

    public function isAjax()
    {
        if (ApplicationContext::isCli()) {
            return false;
        }

        $value = $this->server('HTTP_X_REQUESTED_WITH');
        return (!is_null($value) && strtolower($value) == 'xmlhttprequest') ? true : false;
    }

    public function getHost()
    {
        return ApplicationContext::isCli() ? "localhost" : $this->server["HTTP_HOST"];
    }

    public function getMethod()
    {
        return ApplicationContext::isCli() ? "" : $this->server['REQUEST_METHOD'];
    }

    /**
     * Return whether or not the method is GET
     *
     * @return boolean
     */
    public function isGet()
    {
        return ApplicationContext::isCli() ? false : ($this->server['REQUEST_METHOD'] == 'GET');
    }

    /**
     * Return whether or not the method is HEAD
     *
     * @return boolean
     */
    public function isHead()
    {
        return ApplicationContext::isCli() ? false : ($this->server['REQUEST_METHOD'] == 'HEAD');
    }

    /**
     * Return whether or not the method is POST
     *
     * @return boolean
     */
    public function isPost()
    {
        return ApplicationContext::isCli() ? false : ($this->server['REQUEST_METHOD'] == 'POST');
    }

    /**
     * Return whether or not the method is PUT
     *
     * @return boolean
     */
    public function isPut()
    {
        return ApplicationContext::isCli() ? false : ($this->server['REQUEST_METHOD'] == 'PUT');
    }

    /**
     * Return whether or not the method is DELETE
     *
     * @return boolean
     */
    public function isDelete()
    {
        return ApplicationContext::isCli() ? false : ($this->server['REQUEST_METHOD'] == 'DELETE');
    }

    /**
     * Return whether or not the method is TRACE
     *
     * @return boolean
     */
    public function isTrace()
    {
        return ApplicationContext::isCli() ? false : ($this->server['REQUEST_METHOD'] == 'TRACE');
    }

    /**
     * Return whether or not the method is OPTIONS
     *
     * @return boolean
     */
    public function isOptions()
    {
        return ApplicationContext::isCli() ? false : ($this->server['REQUEST_METHOD'] == 'OPTIONS');
    }

    /**
     * Return whether or not the method is CONNECT
     *
     * @return boolean
     */
    public function isConnect()
    {
        return ApplicationContext::isCli() ? false : ($this->server['REQUEST_METHOD'] == 'CONNECT');
    }

    /**
     * Return whether or not the method is PATCH
     *
     * @return boolean
     */
    public function isPatch()
    {
        return ApplicationContext::isCli() ? false : ($this->server['REQUEST_METHOD'] == 'PATCH');
    }

    protected function parseData()
    {
        $pData = file_get_contents('php://input');
        $paramData = array();

        if (isset($_SERVER['CONTENT_TYPE']) && (stripos($_SERVER['CONTENT_TYPE'], 'json') !== false)) {
            // If the content-type is JSON
            $paramData = json_decode($pData, true);
        } else if (isset($_SERVER['CONTENT_TYPE']) && (stripos($_SERVER['CONTENT_TYPE'], 'xml') !== false)) {
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
            $paramData = json_decode(json_encode((array)simplexml_load_string($pData)), true);
        } else {
            // Else, default to a regular URL-encoded string
            parse_str($pData, $paramData);
        }

        return $paramData;
    }

    private function getAllHeaders()
    {
        // Get any possible request headers
        if (function_exists('getallheaders')) {
            $this->headers = getallheaders();
        } else {
            foreach ($_SERVER as $key => $value) {
                if (substr($key, 0, 5) == 'HTTP_') {
                    $key = ucfirst(strtolower(str_replace('HTTP_', '', $key)));
                    if (strpos($key, '_') !== false) {
                        $ary = explode('_', $key);
                        foreach ($ary as $k => $v) {
                            $ary[$k] = ucfirst(strtolower($v));
                        }
                        $key = implode('-', $ary);
                    }
                    $this->headers[$key] = $value;
                }
            }
        }
    }
}