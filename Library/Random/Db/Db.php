<?php
/**
 * Created by PhpStorm.
 * User: dengjiaming
 * Date: 24/3/2016
 * Time: 下午9:11
 */

namespace Random\Db;

use Random\Debug;
use Random\IDatabase;

class Db implements IDatabase
{

    /**
     * @var object 数据库连接
     */
    protected $_connLink = array();
    /**
     * @var int 事务数
     */
    protected $transactionCounter = 0;
    /**
     * @var array 事务错误数组
     */
    protected $transactionError = array();
    /**
     * @var string 数据库错误信息
     */
    protected $_error = '';
    /**
     * @var int 插入id
     */
    protected $_insertId = 0;
    /**
     * @var int 影响行数
     */
    protected $_affectedRows = 0;
    /**
     * @var int 数据行数
     */
    protected $_fieldCount = 0;

    /**
     * @var bool 是否开启debug模式
     */
    protected $debug = false;

    /**
     * @var array 默认配置
     */
    protected $config = array(
        // 服务器地址
        'hostname' => 'localhost',
        // 数据库名
        'database' => '',
        // 数据库用户名
        'username' => 'root',
        // 数据库密码
        'password' => '',
        // 数据库连接端口
        'port' => '',
        // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
        'deploy' => 0,
        // 数据库读写是否分离 主从式有效
        'rw_separate' => true,
        // 读写分离后 主服务器数量
        'master_num' => 1,
        // 指定从服务器序号
        'slave_no' => '',
    );

    function __construct($config = array())
    {
        $this->config = array_merge($this->config, $config);
//        $this->type = $this->config['type'];
//        $this->host = $this->config['host'];
//        $this->username = $this->config['username'];
//        $this->password = $this->config['password'];
//        $this->database = $this->config['database'];
//        $this->port = $this->config['port'];
        $this->debug = $this->config['debug'];
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->_error;
    }

    /**
     * @return mixed
     */
    public function getInsertId()
    {
        return $this->_insertId;
    }

    /**
     * @return mixed
     */
    public function getAffectedRows()
    {
        return $this->_affectedRows;
    }

    /**
     * @return mixed
     */
    public function getFieldCount()
    {
        return $this->_fieldCount;
    }

    /**
     * @param $host
     * @param $username
     * @param $password
     * @param $database
     * @param $port
     * @param $type string
     * @author DJM <op87960@gmail.com>
     * @todo 获取数据库连接
     */
    protected function connect($host, $username, $password, $database, $port, $type)
    {
    }

    /**
     * @param $sql
     * @param array $param
     * @author DJM <op87960@gmail.com>
     * @todo 得到关联数组数据
     */
    public function getArray($sql, $param = array())
    {
    }

    /**
     * @param $sql
     * @param array $param
     * @author DJM <op87960@gmail.com>
     * @todo 得到一条数据
     */
    public function getRow($sql, $param = array())
    {
    }

    /**
     * @param $mode
     * @return mixed
     * @author DJM <op87960@gmail.com>
     * @todo 获取数据库连接
     */
    public function getConnection($mode)
    {
        if (empty($this->_connLink)) {
            $this->_connLink['master'] =
                $this->connect($this->config['host'], $this->config['username'],
                    $this->config['password'], $this->config['database'], $this->config['port'], $this->config['type']);
            if ($this->config['deploy'] && !empty($this->config['slave'])) {
                foreach ($this->config['slave'] as $val) {
                    $this->_connLink['slave'][] =
                        $this->connect($val['host'], $val['username'],
                            $val['password'], $val['database'], $val['port'], $val['type']);
                }
            }
        }
        //在事务中默认用主机
        if ($this->transactionCounter) {
            return $this->_connLink['master'];
        } else if ($this->config['deploy']) {
            switch ($mode) {
                case 'r':
                    return $this->randSlave();
                case 'w':
                default :
                    return $this->_connLink['master'];
            }
        } else {
            return $this->_connLink['master'];
        }
    }

    /**
     * @return \PDO
     * @author DJM <op87960@gmail.com>
     * @todo 得到从机
     */
    private function randSlave()
    {
        return $this->_connLink['slave'][0];
    }

    /**
     * @return bool
     * @author DJM <op87960@gmail.com>
     * @todo 事务commit
     */
    function commit()
    {
        if (!--$this->transactionCounter) {
            return $this->getConnection('w')->commit();
        }
        return $this->transactionCounter >= 0;
    }

    /**
     * @return bool
     * @author DJM <op87960@gmail.com>
     * @todo 事务回滚
     */
    function rollback()
    {
        if (--$this->transactionCounter) {
            $this->query('ROLLBACK TO trans' . ($this->transactionCounter + 1));
            return true;
        }
        return $this->getConnection('w')->rollback();
    }

    /**
     * @return bool|void
     * @author DJM <op87960@gmail.com>
     * @todo 开启事务
     */
    function beginTransaction()
    {
        if (!$this->transactionCounter++) {
            $this->transactionError["'$this->transactionCounter'"] = false;
            return $this->begin_transaction();
        }
        $this->query('SAVEPOINT trans' . $this->transactionCounter);
        $this->transactionError["'$this->transactionCounter'"] = false;
        return $this->transactionCounter >= 0;
    }

    /**
     * @return bool
     * @author DJM <op87960@gmail.com>
     * @todo 结束事务
     */
    function endTransaction()
    {
        if ($this->transactionError["'$this->transactionCounter'"]) {
            //把事务的错误赋值给_error,用getError()获取
            $this->_error = $this->transactionError["'$this->transactionCounter'"];
            //销毁这个变量
            unset($this->transactionError["'$this->transactionCounter'"]);
            $result = $this->rollback();
        } else {
            $result = $this->commit();
        }
        //如果是mysqli驱动则还需要调用这个方法去恢复自动提交模式
        if (!$this->transactionCounter && strpos(get_called_class(), 'Mysqli')) {
            $this->getConnection('w')->autocommit(true);
        }
        return $result;
    }

    protected function begin_transaction()
    {

    }

    protected function updateField($conn, $result)
    {
        if ($this->transactionCounter && !$result) {
            $this->transactionError["'$this->transactionCounter'"] = $this->_error;
        }
    }

    /**
     * @param $array
     * @return mixed
     * @author DJM <op87960@gmail.com>
     * @todo 是否关联数组
     */
    private function is_assoc($array = array())
    {
        return !empty($array) && array_keys($array) !== range(0, count($array) - 1);
    }

    /**
     * @param $sql string
     * @param $param array
     * @param $mode string
     * @return mixed
     * @author DJM <op87960@gmail.com>
     * @example query("select * from `user` where `id` = :id", array(':id'=>'2'));
     * @todo sql查询
     */
    function query($sql, $param = array(), $mode = 'r')
    {
        $conn = $this->getConnection($mode);
        
        /** @var  $statement \PDOStatement */
        $statement = $conn->prepare($sql);

        //根据数组类型,来判断占位符的形式.
        $len = count($param);
        $flag = '';
        if (strpos($sql, ':')) {
            $flag = ':';
        }
        if ($len) {
            for ($i = 0; $i < $len; $i++) {
                $type = isset($this->_type[$param[$i][2]]) ? $this->_type[$param[$i][2]] : \PDO::PARAM_STR;
                $statement->bindParam($flag . $param[$i][0], $param[$i][1], $type);
            }
            unset($keys);
        }

        if ($this->debug) {
            Debug::startCount();
        }

        $statement->execute();

        if ($this->debug) {
            $str = $statement->queryString;
            if (!empty($param)) {
                for ($i = 0; $i < $len; $i++) {
                    $str = str_replace($flag . $param[$i][0], $param[$i][1], $str);
                }
            }

            Debug::endCount($str);
        }

        $this->updateField($conn, $statement);
        return $statement;
    }

    public function execute($sql, $option = array())
    {
    }


    /**
     * @author DJM <op87960@gmail.com>
     * @todo 关闭连接
     */
    function close()
    {
        $this->_connLink['master'] = null;
        if (isset($this->_connLink['slave']) && !empty($this->_connLink['slave'])) {
            foreach ($this->_connLink['slave'] as &$value) {
                $value = null;
            }
        }
    }

    function __destruct()
    {
        $this->close();
    }
}