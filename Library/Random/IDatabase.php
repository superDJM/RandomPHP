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

    function connect($host, $username, $password, $database, $port);

    function query($sql, $param);

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

    public function getConnection();

    public function getArray($sql, $param = array());

    public function getRow($sql, $param = array());

    function close();
}