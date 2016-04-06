<?php
/**
 * Created by PhpStorm.
 * User: qiming.c <qiming.c@foxmail.com>
 * Date: 2016/4/6
 * Time: 20:09
 */

namespace Random;


class Model extends SqlBuilder
{

    /**
     * @todo 执行链式操作
     * @return array or boolean
     * @example  $ModelObject->select()->execute();
     */
    public function execute()
    {
        $sql = $this->buildSql();
        return $this->query($sql);
    }

    /**
     * @param $sql
     * @todo 不使用链式操作，直接执行sql语句
     * @return array or boolean
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
     * @todo 返回单条数据
     * @return array
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