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

    public function getArray($sql);

    public function getRow($sql);

    function close();
}