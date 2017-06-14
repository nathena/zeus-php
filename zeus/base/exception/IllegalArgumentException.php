<?php
/**
 * Created by IntelliJ IDEA.
 * User: nathena
 * Date: 2017/6/14
 * Time: 20:56
 */

namespace zeus\base\exception;


class IllegalArgumentException extends NestedException
{
    public function __construct ($message = null, $code = null, $previous = null)
    {
        parent::__construct($message,$code,$previous);
    }
}