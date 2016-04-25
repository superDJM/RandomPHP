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


    function __construct($dir)
    {
        $this->dir = $dir;
        $this->com_dir = Config::get('path.TPL_TEMP_ROOT');
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
            if (Config::get('debug') || !file_exists($comFile) || filemtime($comFile) < filemtime($path)) {
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
        $isextend = $this->isextend($content);
        //继承及block内容替换，得到父模板以及把block替换后的内容
        if ($isextend['count']) {
            $content    = $this->extend($content);
        }
        // var_dump($content);
        //替换引用，得到引用后内容
        $isinclude = $this->isinclude($content);
        $include = $this->getinclude($isinclude,$content);
        $content = preg_replace($include['includerp'],$include['includecontent'],$content);
        // var_dump($content);
        $content = $this->qublock($content);
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
        $isextend = $this->isextend($content);
        $isblock = $this->isblock($content);
        //继承就获得继承的内容
        if ($isextend['count']) {
            $content = $this->getextend($isextend['extendname'][0][1],$content);
        }
        $blockrp = $this->getblockrp($isblock); //得到block替换的正则及替换
        $content = preg_replace($blockrp,$isblock['blockcontent'][0],$content);

        return $content;
    }
    /**
     * @param $isblock
     * @author  MZ
     * @todo 获取替换block的正则表达式
     */
    function getblockrp($isblock)
    {
        for ($i=0; $i < count($isblock['blockname']); $i++) {
            $blockrp[] = '/{\s*block\s*name\s*=\s*["\']'.$isblock['blockname'][$i][1].'\s*["\']\s*}.*{\s*\/\s*block\s*}/Us';
        }
        return $blockrp;
    }
    /**
     * @param $content
     * @author  MZ
     * @todo 获取继承的文件内容（除去无关东西）
     */
    function getextend($extendname,$content)
    {
        $extendpath = $this->dir . '/' . $extendname ;

        if (file_exists($extendpath)) {
            $exdconp = file_get_contents($extendpath);
            return $this->extend($exdconp);
        }else{
            echo "错误";
            return null;
        }

    }
    /**
     * @param $isinclude $content
     * @author  MZ
     * @todo 获取引用内容及替换正则
     */
    function getinclude($isinclude,$content)
    {
        for ($i=0; $i < count($isinclude['includename']); $i++) {
            $includepath = $this->dir .'/'.$isinclude['includename'][$i][1];
            if (file_exists( $includepath)) {
                $includecontent[] = file_get_contents($includepath);
            }else{
                $includecontent[] = null;
            }
            $includerp[] = '/{\s*include\s*name\s*=\s*[\'"].*'.$isinclude['includename'][$i][1].'[\'"]\s*}/Us';
        }
        return ['includecontent'=>$includecontent,'includerp'=>$includerp];
    }
    /**
     * @param  $content
     * @author  MZ
     * @todo $extendname[0][0]==模板值,返回匹配模板名数字及继承名
     */
    function isextend($content)
    {
        $content  = $this->quitnote($content);
        $extendrp = '/{\s*extend\s*name\s*=\s*[\'"].*[\'"]\s*}/Us';
        $count    = preg_match_all($extendrp, $content ,$extendHtml);
        $extendname = $this->getfilename($extendHtml);
        return ['count'=>$count,'extendname'=>$extendname];
    }
    /**
     * @param  $content
     * @author  MZ
     * @todo $blockname[*][1];$blockcontent[0][*]为block名和内容,返回匹配名数字、名字和内容
     */
    function isblock($content)//$blockname[*][1];$blockcontent[0][*];
    {
        $content = $this->quitnote($content);
        $blkrpl = '/{\s*block\s*name\s*=\s*[\'"].*[\'"]\s*}.*{\s*\/\s*block\s*}/Us';     //获取代码块
        $blkrps = '/{\s*block\s*name\s*=\s*[\'"].*[\'"]\s*}/';
        $count = preg_match_all($blkrpl, $content ,$blockcontent);
        if ($count) {
            preg_match_all($blkrps, $content ,$blkname);
            $blockname=$this->getfilename($blkname);
        }
        return ['count'=>$count,'blockname'=>$blockname,'blockcontent'=>$blockcontent];
    }
    /**
     * @param  $content
     * @author  MZ
     * @todo $includename[*][0]==引用模板值,返回匹配引用字数和模板名
     */
    function isinclude($content) //$includename[*][0]==模板值
    {
        $content  = $this->quitnote($content);
        $includerp = '/{\s*include\s*name\s*=\s*[\'"].*[\'"]\s*}/Us';
        $count    = preg_match_all($includerp, $content ,$includeHtml);
        $includename = $this->getfilename($includeHtml);
        return ['count'=>$count,'includename'=>$includename];
    }
    /**
     * @param  $content
     * @author  MZ
     * @todo 去注释函数
     */
    function quitnote($content){
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
    function qublock($content)
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
    function getfilename($isexd){
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