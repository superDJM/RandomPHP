<?php
/**
 * Created by PhpStorm.
 * User: dengjiaming
 * Date: 23/3/2016
 * Time: 下午4:06
 */

namespace Random\Http;

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

        unset($_REQUEST);

        //cli?
        if (PHP_SAPI != 'cli') {
            //get Server
            $this->parseServer();
            //get Cookies
            $this->parseCookies();

            //get Session
            $this->parseSession();
        }


    }

    static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function parseServer()
    {
        $this->_server = $_SERVER;
        unset($_SERVER);
    }

    private function parsePost()
    {
        if (!empty($_POST)) {
            define('IS_POST', true);
            $this->_post = $_POST;
        } else {
            define('IS_POST', false);
        }
        unset($_POST);
    }

    private function parseGet()
    {
        if (!empty($_GET)) {
            if (IS_POST) {
                define('IS_GET', false);
            } else {
                define('IS_GET', true);
            }
            $this->_get = $_GET;
        }
        unset($_GET);
    }

    private function parseCookies()
    {
        $this->_cookies = $_COOKIE;
        unset($_COOKIE);
    }

    private function parseSession()
    {
        $this->_session = $_SESSION;
        $_SESSION = null;
    }

    //get private variable
    public function __get($name)
    {
        $name = "_" . $name;
        if (isset($this->$name)) {
            return $this->$name;
        } else {
            return;
        }
    }

    public function __call($name, $arguments)
    {
        switch ($name) {
            case 'session' :
                if (($count = count($arguments)) == 1) {
                    return isset($this->_session[$this->session_suffix . "_" . $arguments[0]]) ? $this->_session[$this->session_suffix . "_" . $arguments[0]] : '';
                } elseif ($count == 2) {
                    $this->_session[$this->session_suffix . "_" . $arguments[0]] = $arguments[1];
                    return;
                } else {
                    trigger_error("session() takes two arguments at most , $count given");
                    return;
                }
                break;
            case 'cookie' :
                if (($count = count($arguments)) == 1) {
                    return isset($this->_cookies[$this->cookies_suffix . "_" . $arguments[0]]) ? $this->_cookies[$this->cookies_suffix . "_" . $arguments[0]] : '';
                } elseif ($count == 2) {
                    $this->_cookies[$this->cookies_suffix . "_" . $arguments[0]] = $arguments[1];
                    return;
                } else {
                    trigger_error("cookie() takes two arguments at most , $count given");
                    return;
                }
                break;
            default :
                trigger_error("$name is not defined");
        }
    }

    public function __destruct()
    {
        $_SESSION = $this->_session;
        $_COOKIE = $this->_cookies;
    }
}