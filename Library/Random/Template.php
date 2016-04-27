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
    protected $tplname = array();
    protected $tpldata = array();
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

    private $_config = array();

    function __construct($config, $dir)
    {
        $this->_config = $config;
        $this->dir = $dir;
        $this->com_dir = $this->_config['path']['TPL_TEMP_ROOT'];
        if (!is_dir($this->com_dir) && !mkdir($this->com_dir,0777,true)) {
            throw new Exception('RandomPHP can not create the compile_dir in '.$this->com_dir);
        }
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
            if ($this->_config['debug'] || !file_exists($comFile) || filemtime($comFile) < filemtime($path)) {
                $content = file_get_contents($path);
                $repContent = $this->extendTemplate($content);
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

    /**
     * @param $content
     * @author  MZ
     * @todo 继承、替换、引用
     */
    function extendTemplate ($content = ''){
        $isExtend = $this->isExtend($content);
        //继承及block内容替换，得到父模板以及把block替换后的内容
        if ($isExtend['count']) {
            $content    = $this->extend($content);
        }
        // var_dump($content);
        //替换引用，得到引用后内容
        $isInclude = $this->isInclude($content);
        $include = $this->getInclude($isInclude,$content);
        $content = preg_replace($include['includeGrep'],$include['includeContent'],$content);
        // var_dump($content);
        $content = $this->quitBlock($content);
        return $content;
    }

    /**
     * @param $content
     * @author  MZ
     * @todo 继承及替换block
     */
    function extend($content)
    {
        //提前获取是否继承和替换
        $isExtend = $this->isExtend($content);
        $isBlock = $this->isBlock($content);
        //继承就获得继承的内容
        if ($isExtend['count']) {
            $content = $this->getContent($isExtend['extendName'][0][1],$content);
        }
        $blockGrep = $this->getBlockGrep($isBlock); //得到block替换的正则及替换
        $content = preg_replace($blockGrep,$isBlock['blockContent'][0],$content);

        return $content;
    }
    /**
     * @param $isBlock
     * @author  MZ
     * @todo 获取替换block的正则表达式
     */
    function getBlockGrep($isBlock)
    {
        for ($i=0; $i < count($isBlock['blockname']); $i++) {
            $blockGrep[] = '/{\s*block\s*name\s*=\s*["\']'.$isBlock['blockname'][$i][1].'\s*["\']\s*}.*{\s*\/\s*block\s*}/Us';
        }
        return $blockGrep;
    }
    /**
     * @param $content
     * @author  MZ
     * @todo 获取继承的文件内容（除去无关东西）
     */
    function getContent($extendName,$content)
    {
        $extendPath = $this->dir . '/' . $extendName ;

        if (file_exists($extendPath)) {
            $exdconp = file_get_contents($extendPath);
            return $this->extend($exdconp);
        }else{
            echo "错误";
            return null;
        }

    }
    /**
     * @param $isInclude $content
     * @author  MZ
     * @todo 获取引用内容及替换正则
     */
    function getInclude($isInclude,$content)
    {
        for ($i=0; $i < count($isInclude['includeName']); $i++) {
            $includePath = $this->dir .'/'.$isInclude['includeName'][$i][1];
            if (file_exists( $includePath)) {
                $includeContent[] = file_get_contents($includePath);
            }else{
                $includeContent[] = null;
            }
            $includeGrep[] = '/{\s*include\s*name\s*=\s*[\'"].*'.$isInclude['includeName'][$i][1].'[\'"]\s*}/Us';
        }
        return ['includeContent'=>$includeContent,'includeGrep'=>$includeGrep];
    }
    /**
     * @param  $content
     * @author  MZ
     * @todo $extendName[0][0]==模板值,返回匹配模板名数字及继承名
     */
    function isExtend($content)
    {
        $content  = $this->quitNote($content);
        $extendGrep = '/{\s*extend\s*name\s*=\s*[\'"].*[\'"]\s*}/Us';
        $count    = preg_match_all($extendGrep, $content ,$extendHtml);
        $extendName = $this->getFileName($extendHtml);
        return ['count'=>$count,'extendName'=>$extendName];
    }
    /**
     * @param  $content
     * @author  MZ
     * @todo $blockname[*][1];$blockContent[0][*]为block名和内容,返回匹配名数字、名字和内容
     */
    function isBlock($content)//$blockname[*][1];$blockContent[0][*];
    {
        $content = $this->quitNote($content);
        $blkrpl = '/{\s*block\s*name\s*=\s*[\'"].*[\'"]\s*}.*{\s*\/\s*block\s*}/Us';     //获取代码块
        $blkrps = '/{\s*block\s*name\s*=\s*[\'"].*[\'"]\s*}/';
        $count = preg_match_all($blkrpl, $content ,$blockContent);
        if ($count) {
            preg_match_all($blkrps, $content ,$blkname);
            $blockname=$this->getFileName($blkname);
        }
        return ['count'=>$count,'blockname'=>$blockname,'blockContent'=>$blockContent];
    }
    /**
     * @param  $content
     * @author  MZ
     * @todo $includeName[*][0]==引用模板值,返回匹配引用字数和模板名
     */
    function isInclude($content) //$includeName[*][0]==模板值
    {
        $content  = $this->quitNote($content);
        $includeGrep = '/{\s*include\s*name\s*=\s*[\'"].*[\'"]\s*}/Us';
        $count    = preg_match_all($includeGrep, $content ,$includeHtml);
        $includeName = $this->getFileName($includeHtml);
        return ['count'=>$count,'includeName'=>$includeName];
    }
    /**
     * @param  $content
     * @author  MZ
     * @todo 去注释函数
     */
    function quitNote($content){
        $note1 = '/\/(\*).*(\*)\//Us';   //去掉/**/
        $note2 = '/<!--.*-->/Us';         //去掉<!-- --!>
        $content = preg_replace($note1,null,  $content);
        $content = preg_replace($note2,null,  $content);
        return $content;
    }
    /**
     * @param  $content
     * @author  MZ
     * @todo 去block函数{block name = 'sa'}和{/block}
     */
    function quitBlock($content)
    {
        $blkfirst = '/{\s*block\s*name\s*=\s*["\'].*["\']\s*}/Us';
        $blkend = '/{\s*\/\s*block\s*}/Us';
        $content = preg_replace($blkfirst,null,$content);
        $content = preg_replace($blkend,null,$content);
        return  $content;
    }

    /**
     * @param  $content
     * @author  MZ
     * @todo  获取文件名函数，去掉‘’和无关的东西
     */
    function getFileName($isexd){
        // var_dump($isexd);
        if($isexd[0]){
            for ($i=0; $i < count($isexd[0]); $i++) {
                preg_match_all("/['\"].*['\"]/",$isexd[0][$i],$arr);
                $arr2[] = preg_split("/['\"]/", $arr[0][0]);//去字符串''
            }
            return $arr2;
        }else{
            return null;
        }
    }
}