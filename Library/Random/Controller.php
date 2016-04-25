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
    protected $module;
    protected $controller;
    protected $method;

    protected $config;
    
    /** @var string 模版目录 */
    protected $template_dir;

    /** @var  Template 视图 */
    private $_view;

    public function __construct($config, $module, $controller, $method)
    {
        $this->config = $config;
        $this->module = $module;
        $this->controller = $controller;
        $this->method = $method;
        $this->template_dir = $this->config['path']['APP_ROOT'] . '/' . $module . '/View/' . $controller;
    }

    /**
     * @param $key
     * @param $value
     * @author DJM <op87960@gmail.com>
     * @todo 调用template进行模版赋值
     */
    public function assign($key, $value = NULL)
    {
        $this->getView()->assign($key, $value);
    }

    /**
     * @param string $file
     * @author DJM <op87960@gmail.com>
     * @return string
     * @todo 调用template进行模版输出
     */
    public function display($file = '')
    {
        //如果不传入文件名,即默认输出与方法同名的模版
        if (empty($file)) {
            $file = $this->method;
        }
        return $this->getView()->display($file);
    }

    private function getView()
    {
        if (is_null($this->_view)) {
            $this->_view = new Template($this->config, $this->template_dir);
        }
        return $this->_view;
    }
}