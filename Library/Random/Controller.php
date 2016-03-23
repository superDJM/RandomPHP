<?php
/**
 * Created by PhpStorm.
 * User: dengjiaming
 * Date: 24/3/2016
 * Time: 上午1:11
 */

namespace Random;


class Controller
{
    protected $data;
    protected $module;
    protected $controller;
    protected $template_dir;
    protected $method;

    function __construct($module, $controller, $method)
    {
        $this->module = $module;
        $this->controller = $controller;
        $this->method = $method;
        $this->template_dir = APP_ROOT . '/' . $module . '/View/' . $controller;
    }

    function assign($key, $value)
    {
        $this->data[$key] = $value;
    }

    function display($file = '')
    {
        if (empty($file)) {
            $file = strtolower($this->method) . '.html';
        }
        $path = $this->template_dir . '/' . $file;
        extract($this->data);
        include $path;
    }
}