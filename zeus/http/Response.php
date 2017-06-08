<?php

namespace zeus\http;

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

    private $code;
    private $message;
    private $body;
    private $version;
    private $headers;

    public function __construct($version = '1.1')
    {
        $this->version = $version;
        $this->code = 200;
        $this->message = self::$responseCodes[$this->code];
    }


    /**
     * Send redirect
     *
     * @param  string $url
     * @param  string $code
     * @param  string $version
     * @throws Exception
     * @return void
     */
    public static function redirect($url, $code = '302', $version = '1.1')
    {
        if (headers_sent()) {
            throw new \RuntimeException('The headers have already been sent.');
        }

        header("HTTP/{$version} {$code} " . self::$responseCodes[$code]);
        header("Location: {$url}");
    }


    public function getHeadersAsString($status = true, $br = "\n")
    {
        $headers = '';
        if ($status) {
            $headers = "HTTP/{$this->version} {$this->code} {$this->message}{$br}";
        }
        foreach ($this->headers as $name => $value) {
            $headers .= "{$name}: {$value}{$br}";
        }
        return $headers;
    }


    public function setCode($code)
    {
        if (!array_key_exists($code, self::$responseCodes)) {
            throw new Exception('That header code ' . $code . ' is not allowed.');
        }

        $this->code = $code;
        $this->message = self::$responseCodes[$code];

        return $this;
    }

    public function setBody($body = null)
    {
        $this->body = $body;
        return $this;
    }

    public function setHeader($name, $value)
    {
        $this->headers[$name] = $value;

        return $this;
    }

    public function setHeaders(array $headers)
    {
        foreach ($headers as $name => $value) {
            $this->headers[$name] = $value;
        }

        return $this;
    }

    public function setSslHeaders()
    {
        $this->headers['Expires'] = 0;
        $this->headers['Cache-Control'] = 'private, must-revalidate';
        $this->headers['Pragma'] = 'cache';

        return $this;
    }

    public function sendHeaders()
    {
        header("HTTP/{$this->version} {$this->code} {$this->message}");
        foreach ($this->headers as $name => $value) {
            header($name . ": " . $value);
        }
    }


    public function send()
    {
        if (headers_sent()) {
            throw new \RuntimeException('The headers have already been sent.');
        }

        switch ($this->headers['Content-Type']) {
            case "application/json":
                $this->sendJson();
                break;
            case "application/xml":
                $this->sendXml();
                break;
            default:
                $this->sendBody();
        }
    }

    protected function sendBody()
    {
        if (!isset($this->headers["Content-Type"])) {
            $this->headers["Content-Type"] = "text/plain";
        }
        if (array_key_exists('Content-Encoding', $this->headers)) {
            $body = $this->encodeBody($this->body, $this->headers['Content-Encoding']);
        } else {
            $body = $this->body;
        }
        $this->headers['Content-Length'] = strlen($body);
        $this->sendHeaders();
        echo $body;
    }

    protected function sendJson()
    {
        $this->sendHeaders();
        $data = [
            'code' => $this->code,
            'message' => $this->message,
            'body' => $this->body,
        ];
        echo json_encode($data);
    }

    protected function sendXml()
    {
        $this->sendHeaders();
        $xml = "<xml><code>{$this->code}</code><message><![CDATA[{$this->message}]]></message><body><![CDATA[{$this->body}]]></body></xml>";

        echo $xml;
    }

    private function encodeBody($body, $encode = 'gzip')
    {
        switch ($encode) {
            // GZIP compression
            case 'gzip':
                if (!function_exists('gzencode')) {
                    throw new \RuntimeException('Gzip compression is not available.');
                }
                $encodedBody = gzencode($body);
                break;
            // Deflate compression
            case 'deflate':
                if (!function_exists('gzdeflate')) {
                    throw new \RuntimeException('Deflate compression is not available.');
                }
                $encodedBody = gzdeflate($body);
                break;
            // Unknown compression
            default:
                $encodedBody = $body;
        }
        return $encodedBody;
    }

}