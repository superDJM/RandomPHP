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

    /** @var array 模版匹配规则 */
    protected $pattern = array(
        /*普通变量
        {$name}  => <?php echo $name; ?>
        */
        '/{\$([a-zA-Z]*)}/i',
        /*
        {if}{else}{/if}标签
        {if $name == 1}   =>  <?php if ($name == 1) {
            yes           =>      echo 'yes';
        {else }           =>  } else {
            no            =>      echo 'no';
        {/if}             =>  }?>
        */
        '/{if\s+(.+)\s*}([^.]*.+[^.]*){else\s*}([^.]*.+[^.]*){\/if\s*}/U',
        /*
        {if}{/if}标签
        {if $name == b}   =>  <?php if ($name == b) {
            yes           =>       echo 'yes';
        {/if}             =>  }?>
        */
        '/{if\s+(.+)\s*}([^.]*.+[^.]*){\/if\s*}/U',
        /*
        {foreach $obj as $key => $val}  =>  <?php foreach ($obj as $ ) {
            val:$val                    =>      echo "val:$val";
        {/foreach}                      =>  } ?>
         */
        '/{foreach\s+(\$[a-zA-Z]+)\s+as\s+(\$[a-zA-Z]+|\$[a-zA-Z]+\s+=>\s+\$[a-zA-Z]+)\s*}([^.]*.+[^.]*){\/foreach\s*}/U',
    );

    /** @var array 模版替换规则 */
    protected $replacement = array(
        "<?php echo \$\\1; ?>",
        "<?php if (\\1) { echo '\\2'; } else { echo '\\3'; } ?>",
        "<?php if (\\1) { echo '\\2'; } ?>",
        '<?php foreach (\\1 as \\2) { echo "\\3"; } ?>',
    );


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

    /**
     * @param $file
     * @return string|void
     * @throws Exception
     * @author DJM <op87960@gmail.com>
     * @todo 模版输出
     */
    function display($file)
    {
        $path = $this->dir . '/' . $file . '.html';

        //解压变量
        extract($this->data, EXTR_OVERWRITE);

        //unset arr
        unset($this->data);

        //编译后的tpl,暂时为写文件
        $comFile = $this->com_dir . '/' . md5($file) . '.tpl';

        //在debug模式下存在模版文件则进行编译
        if (file_exists($path)) {
            if (Config::get('debug') || !file_exists($comFile) || filemtime($comFile) < filemtime($path)) {
                $content = file_get_contents($path);
                $pattern = array(
                    '/{\$([a-zA-Z]*)}/i'
                );
                $replacement = array(
                    '<?php echo \$${1}; ?>'
                );
                $repContent = $content;
                //模版的替换
                /*preg_replace_callback($pattern, function ($match) use (&$repContent) {
                    $this->data[$match[1]] = empty($this->data[$match[1]]) ? '' : $this->data[$match[1]];
                    $repContent = str_replace($match[0], "<?php echo \$$match[1]; ?>", $repContent);
                }, $content);*/

                $repContent = preg_replace($this->pattern, $this->replacement, $repContent);

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