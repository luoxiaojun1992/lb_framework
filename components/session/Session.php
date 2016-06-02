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
                session_set_save_handler(
                    array($mysql_session, 'open'),
                    array($mysql_session, 'close'),
                    array($mysql_session, 'read'),
                    array($mysql_session, 'write'),
                    array($mysql_session, 'destroy'),
                    array($mysql_session, 'gc')
                );
                // 下面这行代码可以防止使用对象作为会话保存管理器时可能引发的非预期行为
                register_shutdown_function('session_write_close');
                break;
            default:
                return;
        }
    }
}
