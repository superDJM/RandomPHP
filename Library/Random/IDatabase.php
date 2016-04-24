<?php
/**
 * Created by PhpStorm.
 * User: dengjiaming
 * Date: 23/3/2016
 * Time: 下午9:43
 */

namespace Random;


interface IDatabase
{

    function query($sql, $param, $mode);

    function commit();

    function rollback();

    function beginTransaction();

    function endTransaction();

    public function getError();

    /**
     * @return mixed
     */
    public function getInsertId();

    /**
     * @return mixed
     */
    public function getAffectedRows();

    /**
     * @return mixed
     */
    public function getFieldCount();

    function getConnection($mode);

    public function execute($sql, $option = array());
    
    public function getArray($sql, $option = array());

    public function getRow($sql, $option = array());

    function close();
}