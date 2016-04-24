<?php 
/**
 * Created by PhpStorm.
 * User: qiming.c <qiming.c@foxmail.com>
 * Date: 2016/4/13
 * Time: 20:46
 */

namespace Random\Cache;
use Random\Exception;
use Random\IDataCache;

/**
 * 实现文件缓存类
 */

class FileCache implements IDataCache
{
    protected $_dir;
    protected $_prefix;

    /**
     * @var array 默认配置
     */
    protected $_config = array(
        'dir' => '/tmp/cache',
        'prefix' => 'randomphp',
    );

    /**
     * FileCache constructor.
     * @param $config array 配置项
     * @throws Exception
     */
    public function __construct($config = array())
    {
        $this->_config = array_merge($this->_config, $config);

        $this->_dir = $this->_config['dir'];
        $this->_prefix = $this->_config['prefix'];
        if (!is_dir($this->_dir)) {
            if (!mkdir($this->_dir, 0775, true)) {
                throw new Exception("创建" . $this->_dir . "文件夹失败");
            }
            chmod($this->_dir, 0775);
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
        return $this->pushContents($file, $data, (int)($lifetime));
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
        $data = $this->getContents($file);
        if (!empty($data)) {
            return $data;
        }
        return null;
    }

    /**
     * @todo 删除一条缓存
     */
    public function remove($key)
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
        $files = scandir($this->_dir);
        if ($files) {
            foreach ($files as $file) {
                if ($file != '.' && $file != '..' && is_file($this->_dir . '/' . $file)) {
                    unlink($this->_dir . '/' . $file);   
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
        return $this->_dir . '/' . $this->key2FileName($key);
    }

    protected function key2FileName($key)
    {
        return md5($this->_prefix . "$key");
    }

    protected function getContents($file)
    {
        $contents = file_get_contents($file);

        if ($contents) {
            if ( function_exists('gzuncompress')){
                $contents = gzuncompress($contents);
            }
            $contents =  unserialize($contents);
            if ($contents['time'] == 0 || $contents['time'] > time()){
                return $contents['data'];
            } else {
                unlink($file);
                return null;
            }
        } else {
            return null;
        }
    }

    protected function pushContents($file, $data, $lifetime)
    {
        $contents['time'] = $lifetime == 0 ? 0 : time() + $lifetime;
        $contents['data'] = $data;
        $contents = serialize($contents);
        if (function_exists('gzcompress')) {
            $contents = gzcompress($contents);
        }
        if (is_dir(dirname($file)) && is_writable(dirname($file))) {
            $result = file_put_contents($file, $contents);
            chmod($file, 0775);
        } else {
            throw new Exception($file . '没有权限写入.');
        }
        if ($result) {
            return true;
        } else {
            return false;
        }
    }
}