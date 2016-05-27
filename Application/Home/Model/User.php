<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/26
 * Time: 16:02
 */

namespace Home\Model;

use Random\Orm;

class User extends Orm
{

    protected static $table = 'user';

    protected static $pk = 'id';

    protected static $fields = array(
        'id' => 'int',
        'name' => 'varchar',
        'profile_id' => 'int',
    );
}
