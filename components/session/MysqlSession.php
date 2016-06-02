<?php
/**
 * Created by PhpStorm.
 * User: luoxiaojun
 * Date: 16/6/2
 * Time: 下午8:37
 * Lb framework mysql session component file
 */

namespace lb\components\session;

use lb\BaseClass;
use lb\models\LbSession;

class MysqlSession extends BaseClass
{
    protected static $_instance = false;

    private function __construct()
    {

    }

    public function __clone()
    {
        // TODO: Implement __clone() method.
    }

    public function open($save_path, $session_name)
    {
        return(true);
    }

    public function close()
    {
        return(true);
    }

    public function read($id)
    {
        $lb_session = LbSession::model()->findByPk($id);
        if ($lb_session) {
            return $lb_session->data;
        }
        return '';
    }

    public function write($id, $sess_data)
    {
        $lb_session = LbSession::model()->findByPk($id);
        if ($lb_session) {
            $lb_session->setAttributes([
                'data' => $sess_data,
                'expire' => time(),
            ]);
            if ($lb_session->save()) {
                return true;
            }
        }
        return false;
    }

    public function destroy($id)
    {
        if (LbSession::model()->deleteByPk($id)) {
            return true;
        }
        return false;
    }

    public function gc($maxlifetime)
    {
        $now_time = time();
        $expire_time = $now_time - $maxlifetime;
        if (LbSession::model()->deleteByConditions(['expire' => ['<' => $expire_time]])) {
            return true;
        }
        return false;
    }

    public static function component()
    {
        if (property_exists(get_called_class(), '_instance')) {
            if (static::$_instance instanceof static) {
                return static::$_instance;
            } else {
                $new_mysql_session = new static();
                static::$_instance = $new_mysql_session;
                return static::$_instance;
            }
        }
        return false;
    }
}
