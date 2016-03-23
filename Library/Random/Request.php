<?php
/**
 * Created by PhpStorm.
 * User: dengjiaming
 * Date: 23/3/2016
 * Time: 下午4:06
 */

namespace Random;

/**
 * Class Request
 * @package Random
 * @author DJM <op87960@gmail.com>
 * @todo http请求类
 */
class Request
{

    public $get;
    public $post;
    public $header;

    static $instance;

    private function __construct()
    {
        if (!empty($_POST)) {
            define('IS_POST', true);
            $this->post = $_POST;
            unset($_POST);
        } else {
            define('IS_POST', false);
        }

        if (!empty($_GET)) {
            if (IS_POST) {
                define('IS_GET', false);
            } else {
                define('IS_GET', true);
            }
            $this->get = $_GET;
            unset($_GET);
        }

    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}