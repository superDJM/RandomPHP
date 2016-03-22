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
        require __DIR__ . '/Autoload.php';
        $classLoader = new Autoload();
        $classLoader->register();
        $classLoader->addNamespace('Random\\', APP_ROOT . '/Library/Random');

        //载入配置
        $config = Factory::getConfig(__DIR__);

        //根据配置载入命名空间
        foreach ($config['router'] as $key => $value) {
            $classLoader->addNamespace($key, APP_ROOT . $value);
        }

        //加载库全局函数
        require __DIR__ . '/Common/functions.php';

        if (DEBUG) {
            ini_set("display_errors", "On");
            error_reporting(E_ALL | E_STRICT);
        } else {
            ini_set("display_errors", "Off");
            ini_set("log_errors", APP_ROOT . '/Temp/Logs/error_log.log');
        }

        return $this;
    }


    /**
     * @author DJM <op87960@gmail.com>
     * @todo 路由分派
     */
    function dispatch()
    {
//        $uri = $_SERVER['SCRIPT_NAME'];
        $uri = $_SERVER['PATH_INFO'];   //使用path_info模式
        $request = explode('/', trim($uri, '/'));

        //get Controller
        $controller = ucwords($request[0]) . '\\Controller\\' . ucwords($request[1]);

        if (!class_exists($controller)) {
            echo '404,controller not found';
            exit;
        }

        $class = new $controller();

        //get Method
        $method = $request[2];

        if (!method_exists($controller, $method)) {
            echo '404,method not found';
            exit;
        }

        //执行目标方法
        call_user_func(array($class, $method));

    }
}