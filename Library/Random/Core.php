<?php
/**
 * Created by PhpStorm.
 * User: dengjiaming
 * Date: 22/3/2016
 * Time: 下午2:28
 */

namespace Random;


/**
 * Class Core
 * @package Random
 * @author DJM <op87960@gmail.com>
 * @todo 核心类实现路由分派和初始化
 */
class Core
{

    protected static $instance;

    protected function __construct()
    {
    }

    static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    function init()
    {
        //防止预先输出
        ob_start();

        //开启session
        session_start();

        require __DIR__ . '/Autoload.php';
        $classLoader = new Autoload();
        $classLoader->register();
        $classLoader->addNamespace('Random\\', __DIR__);

        //注册异常处理
        Exception::Register();

        //载入配置
        $config = Factory::getConfig(__DIR__);

        //注册配置
        Register::set('config', $config);

        if (is_array($config['router'])) {
            //根据配置载入命名空间
            foreach ($config['router'] as $key => $value) {
                $classLoader->addNamespace($key, BASE_ROOT . $value);
            }
        }

        //加载钩子
        if (is_array($config['hook'])) {
            foreach ($config['hook'] as $key => $value) {
                Hook::add($key, $value);
            }
        }

        //注册自动载入类
        Register::set('autoload', $classLoader);

        //设置时区
        date_default_timezone_set($config['default_timezone']);

        //加载库全局函数
        require __DIR__ . '/Common/functions.php';

        //设置debug选项
        if (DEBUG) {
            ini_set("display_errors", "on");
            error_reporting(E_ALL | E_STRICT);
        } else {
            ini_set("display_errors", "Off");
            ini_set("log_errors", BASE_ROOT . '/Temp/Logs/error_log.log');
        }

        return $this;
    }


    /**
     * @author DJM <op87960@gmail.com>
     * @todo 路由分派
     */
    function dispatch()
    {

        list($module, $controller, $method) = Router::parseUrl();

        //检查是否存在模块
        if (!is_dir(APP_ROOT . '/' . $module)) {
            throw new \Exception('404,module:' . $module . ' not found');
        }

        //拼凑命名空间
        $controllerNameSpace = $module . '\\Controller\\' . $controller;

        //检查是否存在controller
        if (!class_exists($controllerNameSpace)) {
            throw new \Exception('404,controller:' . $controller . ' not found');
        }

        //检查方法是否存在
        if (!method_exists($controllerNameSpace, $method)) {
            throw new \Exception('404,method:' . $method . ' not found');
        }

        //实例化controller类
        $class = new $controllerNameSpace($module, $controller, $method);

        //Controller的模块目录
        $modulePath = dirname(dirname(Register::get('autoload')->loadClass($controllerNameSpace)));

        //载入配置
        $config = Factory::getConfig($modulePath);

        //载入Request
        $request = Factory::getRequest();

        //钩子
        Hook::listen('APP_START');

        //执行目标方法
        $response = call_user_func(array($class, $method), $config, $request);

        //如果实例方法没有返回Response对象,则new一个空对象,防止send方法调用失败
        if (!($response instanceof Response)) {
            $response = new Response('');
            if (DEBUG) {
                trigger_error('response is no defined!');
            }
        }

        return $response;
    }
}