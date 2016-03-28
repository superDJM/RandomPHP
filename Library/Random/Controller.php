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

    /**
     * @param $key
     * @param $value
     * @author DJM <op87960@gmail.com>
     * @todo 模版赋值
     */
    function assign($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * @param string $file
     * @author DJM <op87960@gmail.com>
     * @return string
     * @todo 模版输入
     */
    function display($file = '')
    {
        //默认输入跟方法对应的tpl
        if (empty($file)) {
            $file = strtolower($this->method) . '.html';
        }
        $path = $this->template_dir . '/' . $file;

        //解压变量
        extract($this->data);

        //编译后的tpl
        $comFileName = BASE_ROOT . '/Temp/Tpl/com_' . strtolower($this->method) . '.php';

        //在debug模式下存在模版文件则进行编译
        if (file_exists($path)) {
            if (DEBUG || !file_exists($comFileName)) {
                $content = file_get_contents($path);
                $pattern = array(
                    '/{\$([a-zA-Z]*)}/i'
                );
                $replacement = array(
                    '<?php echo \$${1}; ?>'
                );
                $repContent = $content;
                //模版的替换
                preg_replace_callback($pattern, function ($match) use (&$repContent) {
                    $this->data[$match[1]] = empty($this->data[$match[1]]) ? '' : $this->data[$match[1]];
                    $repContent = str_replace($match[0], "<?php echo \$this->data['$match[1]']; ?>", $repContent);
                }, $content);
                file_put_contents($comFileName, $repContent);
            }
        } else {
            return;
        }

        include $comFileName;

        $content = ob_get_clean();
        return $content;
    }

}