<?php
/**
 * Created by PhpStorm.
 * User: dengjiaming
 * Date: 13/4/2016
 * Time: 下午8:32
 */

namespace Random\Db;


class Mongodb extends Db
{

    function connect($host, $username, $password, $database, $port)
    {
        if (!isset($this->_conn)) {
//            $server = "mongodb://$username:$password@$host/db:$port";
            $db = new \MongoClient();
            $this->_conn = $db->selectDB($database);
        }
        return $this->_conn;
    }

    function query($sql)
    {
        return $this->_conn->randomphp->find();
    }

    function getRow($sql)
    {
        $this->query();
    }

    function getArray($sql)
    {

    }

    function getError()
    {
        return $this->_conn->lastError();
    }
}
