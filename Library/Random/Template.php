<?php
/**
 * Created by PhpStorm.
 * User: dengjiaming
 * Date: 4/4/2016
 * Time: 上午11:09
 */

namespace Random;


class Template
{

    /** @var  string 模版路径 */
    protected $dir;

    /** @var  string 编译路径 */
    protected $com_dir;

    /** @var  array 模版变量 */
    protected $data = array();

    /** @var array 模版替换规则 */
    protected $pattern = array();


    function __construct($dir)
    {
        $this->dir = $dir;
        $this->com_dir = Config::get('path.TPL_TEMP_ROOT');
    }

    /**
     * @param $key
     * @param $value
     * @author DJM <op87960@gmail.com>
     * @todo 模版赋值
     */
    function assign($key, $value)
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->data[$k] = $v;
            }
        } else {
            $this->data[$key] = $value;
        }
    }

    function display($file)
    {
        $path = $this->dir . '/' . $file . '.html';

        //解压变量
        extract($this->data, EXTR_OVERWRITE);

        //编译后的tpl,暂时为写文件
        $comFile = $this->com_dir . '/' . md5($file) . '.tpl';

        //在debug模式下存在模版文件则进行编译
        if (file_exists($path)) {
            if (DEBUG || !file_exists($comFile) || filemtime($comFile) < filemtime($path)) {
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
                if (is_dir(dirname($comFile)) && is_writable(dirname($comFile))) {
                    file_put_contents($comFile, $repContent);
                    chmod($comFile, 0775);
                } else {
                    throw new Exception($comFile . '没有权限写入.');
                }
            }
        } else {
            return;
        }

        include $comFile;

        $content = ob_get_clean();
        return $content;
    }
}