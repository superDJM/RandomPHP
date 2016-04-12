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

    function query($sql);

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

    public function getArray($sql);

    public function getRow($sql);

    function close();
}