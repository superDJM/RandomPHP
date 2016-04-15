<?php 
/**
 * Created by PhpStorm.
 * User: qiming.c <qiming.c@foxmail.com>
 * Date: 2016/4/13
 * Time: 20:46
 */

namespace Random\Cache;
use Random\Config;

/**
 * 实现文件缓存类
 */

class FileCache
{
    static $_dir;
    static $_prefix;

    public function __construct()
    {
        $dir   = Config::get('file_cache_dir');
        self::$_prefix = Config::get('cache_prefix');
        self::$_dir = BASE_ROOT.$dir.'/FileCache';
        if (!is_dir(self::$_dir)) {
            if( !mkdir(self::$_dir, 0777, true) ){
                trigger_error("创建".self::$_dir."文件夹失败");
            }
        }
    }

    /**
     * @param  string $key
     * @param  mixed $data
     * @param  int $lifetime
     * @return boolean
     * @todo 设置缓存
     */
    public function set($key, $data, $lifetime = 0)
    {
        $file = $this->getFileDir($key);
        $this->pushContents($file, $data, (int)($lifetime));

    }

    /**
     * @param  string $key
     * @return mixed 数据
     * @todo 得到缓存数据
     */
    public function get($key)
    {
        if (!$this->has($key)){
            return null;
        }
        $file = $this->getFileDir($key);  
        $contents = $this->getContents($file);
        if ($contents['time'] == 0 || $contents['time'] > time()){
            return $contents['data'];
        } else {
            unlink($file);
            return null;
        }
    }

    /**
     * @todo 删除一条缓存
     */
    public function delete($key)
    {
        $file = $this->getFileDir($key);
        if ($this->has($key)) {
            return unlink($file);
        } else {
            return false;
        }
    }

    /**
     * @todo 清除目录下所有缓存
     */
    public function clear()
    {
        $files = scandir(self::$_dir);
        if ($files) {
            foreach ($files as $file) {
                if ($file != '.' && $file != '..' && is_file(self::$_dir.'/'.$file)){
                    unlink(self::$_dir.'/'.$file);   
                }
            }
        }
        return true;
    }

    /**
     * @todo 返回缓存是否存在
     */
    protected function has($key)
    {
        $file = $this->getFileDir($key);
        return is_file($file);
    }
    
    protected function getFileDir($key)
    {
        return self::$_dir.'/'.$this->key2FileName($key);
    }

    protected function key2FileName($key)
    {
        return md5(self::$_prefix."$key");
    }

    protected function getContents($file)
    {
        $contents = file_get_contents($file);

        if ($contents) {
            return unserialize($contents);
        } else {
            return false;
        }
    }

    protected function pushContents($file, $data, $lifetime)
    {
        $contents['time'] = $lifetime == 0 ? 0 : time() + $lifetime;
        $contents['data'] = $data;
        $result = file_put_contents($file, serialize($contents));
        if ($result) {
            return true;
        } else {
            return false;
        }
    }
}