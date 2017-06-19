<?php
/**
 * Created by IntelliJ IDEA.
 * User: nathena
 * Date: 2017/6/6 0006
 * Time: 14:07
 */

namespace zeus\http;


class Cookie implements \ArrayAccess
{
    /**
     * @var \zeus\http\Request
     */
    private $request;
    private $domain;
    private $expire = 0;
    private $path = "/";
    private $secure = false;
    private $httponly = true;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->domain = !in_array($this->request->getHost(), ["127.0.0.1", "localhost"]) ? $this->request->getHost() : "";
    }

    public function setDomain($domain)
    {
        $this->domain = $domain;
        return $this;
    }

    public function setExpire($expire)
    {
        $this->expire = $expire;
        return $this;
    }

    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    public function setSecure($secure)
    {
        $this->secure = $secure;
        return $this;
    }

    public function setHttponly($httponly)
    {
        $this->httponly = $httponly;
        return $this;
    }

    public function clear()
    {
        foreach ($_COOKIE as $name => $value) {
            $this->delete($name);
        }
    }

    public function __get($name)
    {
        $value = null;
        if (isset($_COOKIE[$name])) {
            $value = (substr($_COOKIE[$name], 0, 1) == '{') ? json_decode($_COOKIE[$name]) : $_COOKIE[$name];
        }
        return $value;
    }

    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    public function __isset($name)
    {
        return isset($_COOKIE[$name]);
    }

    public function __unset($name)
    {
        $this->delete($name);
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

    protected function set($name, $value)
    {
        if (!is_string($value) && !is_numeric($value)) {
            $value = json_encode($value);
        }
        setcookie($name, $value, $this->expire, $this->path, $this->domain, $this->secure, $this->httponly);
    }

    protected function delete($name)
    {
        if (isset($_COOKIE[$name])) {
            setcookie($name, '', -3600, $this->path, $this->domain, $this->secure, $this->httponly);
            unset($_COOKIE[$name]);
        }
    }
}