<?php
/**
 * Created by PhpStorm.
 * User: dengjiaming
 * Date: 23/3/2016
 * Time: 下午4:06
 */

namespace Random;

/**
 * Class Response
 * @package Random
 * @author DJM <op87960@gmail.com>
 * @todo http应答类
 */
class Response
{

    protected $code;
    protected $context;
    protected $header;
    protected $context_type;
    protected $http_version = 'HTTP/1.1';
    protected $msg;
    protected $charset = 'UTF-8';


    public function __construct($context, $code = 200, $msg = 'OK', $context_type = '')
    {
        ob_start();
        $this->code = $code;
        $this->context = $context;
        $this->context_type = empty($context_type) ? 'text/html' : $context_type;
        $this->msg = $msg;
    }

    public function send()
    {
        $this->sendHeader();
        $this->sendContext();
    }

    public function sendHeader()
    {
        header("$this->http_version $this->code $this->msg");
        header("Content-Type:" . $this->context_type . ';charset=' . $this->charset);
    }

    private function sendContext()
    {
        echo $this->context;
        ob_end_flush();
    }

    /**
     * @return string
     */
    public function getHttpVersion()
    {
        return $this->http_version;
    }

    /**
     * @param string $http_version
     */
    public function setHttpVersion($http_version)
    {
        $this->http_version = $http_version;
    }

    /**
     * @return string
     */
    public function getMsg()
    {
        return $this->msg;
    }

    /**
     * @param string $msg
     */
    public function setMsg($msg)
    {
        $this->msg = $msg;
    }


    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param int $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return mixed
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param mixed $context
     */
    public function setContext($context)
    {
        $this->context = $context;
    }

    /**
     * @return mixed
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @param mixed $header
     */
    public function setHeader($header)
    {
        $this->header = $header;
    }

    /**
     * @return mixed
     */
    public function getContextType()
    {
        return $this->context_type;
    }

    /**
     * @param mixed $context_type
     */
    public function setContextType($context_type)
    {
        $this->context_type = $context_type;
    }
}