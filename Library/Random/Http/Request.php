<?php
/**
 * Created by PhpStorm.
 * User: dengjiaming
 * Date: 23/3/2016
 * Time: 下午4:06
 */

namespace Random\Http;

use Random\Exception;
use Random\Register;

/**
 * Class Request
 * @package Random
 * @author DJM <op87960@gmail.com>
 * @todo http请求类
 */
class Request
{
    private $_get = array();
    private $_post = array();
    private $_header = array();
    private $_file = array();
    private $_session = array();
    private $_cookies = array();
    private $_server = array();
    private $session_suffix;
    private $cookies_suffix;

    protected static $instance;

    private function __construct()
    {
        //get suffix
        $config = Register::get('config');
        $this->session_suffix = $config['session']['suffix'];
        $this->cookies_suffix = $config['cookies']['suffix'];

        //get Post
        $this->parsePost();

        // get Get
        $this->parseGet();

        //unset全局$_REQUEST变量
        unset($_REQUEST);

        //cli?
        if (PHP_SAPI != 'cli') {

            //定义IS_GET IS_POST IS_AJAX
            define('IS_GET', $this->isGet());
            define('IS_POST', $this->isPost());
            define('IS_AJAX', $this->isAjax());
            define('IS_HTTPS', $this->isHttps());

            //get Server
            $this->parseServer();
            //get Cookies
            $this->parseCookies();

            //get Session
            $this->parseSession();
        }
    }

    /**
     * @return string
     * @author DJM <op87960@gmail.com>
     * @todo 获取用户真实ip
     */
    public function getClientIp()
    {
        if (!empty($this->_server["HTTP_CLIENT_IP"])) {
            $cip = $this->_server["HTTP_CLIENT_IP"];
        } elseif (!empty($this->_server["HTTP_X_FORWARDED_FOR"])) {
            $cip = $this->_server["HTTP_X_FORWARDED_FOR"];
        } elseif (!empty($this->_server["REMOTE_ADDR"])) {
            $cip = $this->_server["REMOTE_ADDR"];
        } else {
            $cip = '';
        }
        return $cip;
    }

    /**
     * @return Request
     * @author DJM <op87960@gmail.com>
     * @todo 获取Request静态类
     */
    static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @author DJM <op87960@gmail.com>
     * @todo 解析$_SERVER并unset
     */
    private function parseServer()
    {
        $this->_server = $_SERVER;
        unset($_SERVER);
    }

    /**
     * @author DJM <op87960@gmail.com>
     * @todo 解析$_POST并unset
     */
    private function parsePost()
    {
        $this->_post = $_POST;
        unset($_POST);
    }

    /**
     * @author DJM <op87960@gmail.com>
     * @todo 解析$_GET并unset
     */
    private function parseGet()
    {
        $this->_get = $_GET;
        unset($_GET);
    }

    /**
     * @author DJM <op87960@gmail.com>
     * @todo 解析$_COOKIE并unset
     */
    private function parseCookies()
    {
        $this->_cookies = $_COOKIE;
        unset($_COOKIE);
    }

    /**
     * @author DJM <op87960@gmail.com>
     * @todo 解析$_SESSION并unset
     */
    private function parseSession()
    {
        $this->_session = $_SESSION;
        $_SESSION = null;
    }

    /**
     * @return bool
     * @author DJM <op87960@gmail.com>
     * @todo 判断是否https
     */
    public function isHttps()
    {
        return isset($this->_server['REQUEST_SCHEME']) &&
        $this->_server['REQUEST_SCHEME'] == 'https' ? true : false;
    }

    /**
     * @return bool
     * @author DJM <op87960@gmail.com>
     * @todo 判断是否Ajax提交
     */
    public function isAjax()
    {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
        ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return bool
     * @author DJM <op87960@gmail.com>
     * @todo 判断是否get请求
     */
    public function isGet()
    {
        return $_SERVER['REQUEST_METHOD'] == 'GET' ? true : false;
    }

    /**
     * @return bool
     * @author DJM <op87960@gmail.com>
     * @todo 判断是否post提交
     */
    public function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] == 'POST' ? true : false;
    }


    /**
     * @param $name
     * @param string $method
     * @return string
     * @throws Exception
     * @author DJM <op87960@gmail.com>
     * @todo 获取get或post或server的值
     */
    public function get($name, $method = 'mixed')
    {
        switch ($method) {
            case 'mixed':
            case 'get':
                $result = isset($this->_get[$name]) ? $this->_get[$name] : '';
                //混合模式
                if ($method != 'mixed') {
                    break;
                }
            case 'post':
                $result = isset($this->_post[$name]) ? $this->_post[$name] : (isset($result) ? $result : '');
                break;
            case 'server':
                $result = isset($this->_server[$name]) ? $this->_server : '';
                break;
            default :
                throw new Exception("method get($name,$method) is not supported");
        }
        return $result;
    }

    /**
     * @param $name
     * @param $arguments
     * @return string|void
     * @throws Exception
     * @author DJM <op87960@gmail.com>
     * @todo 实现了session()和cookie()
     */
    public function __call($name, $arguments)
    {
        switch ($name) {
            case 'session' :
                if (($count = count($arguments)) == 1) {
                    return isset($this->_session[$this->session_suffix . "_" . $arguments[0]]) ? $this->_session[$this->session_suffix . "_" . $arguments[0]] : '';
                } elseif ($count == 2) {
                    $this->_session[$this->session_suffix . "_" . $arguments[0]] = $arguments[1];
                    return $arguments[1];
                } else {
                    throw new Exception("session() takes two arguments at most , $count given");
                }
                break;
            case 'cookie' :
                if (($count = count($arguments)) == 1) {
                    return isset($this->_cookies[$this->cookies_suffix . "_" . $arguments[0]]) ? $this->_cookies[$this->cookies_suffix . "_" . $arguments[0]] : '';
                } elseif ($count == 2) {
                    $this->_cookies[$this->cookies_suffix . "_" . $arguments[0]] = $arguments[1];
                    return $arguments[1];
                } else {
                    throw new Exception("cookie() takes two arguments at most , $count given");
                }
                break;
            default :
                throw new Exception("$name is not defined");
        }
    }

    public function __destruct()
    {
        $_SESSION = $this->_session;
        $_COOKIE = $this->_cookies;
    }
}