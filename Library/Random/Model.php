<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/6
 * Time: 19:21
 */

namespace Random;


class Model extends SqlBuilder
{

    /**
     * @todo 执行链式操作
     * @return sql语句执行结果
     * @example  $ModelObject->select()->execute();
     */
    public function execute()
    {
        $sql = $this->buildSql();
        return $this->query($sql);
    }

    /**
     * @todo 不使用链式操作，直接执行sql语句
     * @return sql语句执行结果
     */
    public function query($sql)
    {
        if (preg_match('/SELECT/i', $sql)) {
            return $this->_handle->getArray($sql);
        } else {
            return $this->_handle->query($sql);
        }
    }

    /**
     * @return 单条数据
     */
    public function getOne()
    {
        $sql = $this->buildSql();
        if (preg_match('/SELECT/i', $sql)) {
            return $this->_handle->getRow($sql);
        } else {
            return $this->_handle->query($sql);
        }
    }
}