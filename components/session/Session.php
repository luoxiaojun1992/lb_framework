<?php
/**
 * Created by PhpStorm.
 * User: luoxiaojun
 * Date: 16/6/2
 * Time: 下午8:31
 * Lb framework session component file
 */

namespace lb\components\session;

use lb\BaseClass;

class Session extends BaseClass
{
    public static function set_session($session_type = 'default')
    {
        switch($session_type) {
            case 'default':
                return;
            case 'mysql':
                $mysql_session = MysqlSession::component();
                session_set_save_handler($mysql_session, true);
                break;
            default:
                return;
        }
    }
}
