<?php

namespace lb\components\session;

use lb\components\db\mysql\Dao;
use lb\components\traits\Singleton;
use lb\models\LbSession;

class MysqlSession extends \SessionHandler
{
    use Singleton;

    private function __construct()
    {
        //todo create session table
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
        if ($sess_data) {
            $sess_data = addslashes($sess_data);
            $lb_session = LbSession::model()->findByPk($id);
            $now_time = time();
            $expire_time = $now_time + intval(get_cfg_var('session.gc_maxlifetime'));
            if ($lb_session) {
                $lb_session->setAttributes([
                    'expire' => $expire_time,
                    'data' => $sess_data,
                ]);
                if ($lb_session->save()) {
                    return true;
                }
            } else {
                $sql = 'INSERT INTO `lb_session` (`id`,`expire`,`data`) VALUES("' . $id . '", ' . $expire_time . ', "' . $sess_data . '")';
                $statement = Dao::component()->prepare($sql, 'master');
                if ($statement) {
                    $res = $statement->execute();
                    if ($res) {
                        return true;
                    }
                }
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
        if (LbSession::model()->deleteByConditions(['expire' => ['<' => $maxlifetime]])) {
            return true;
        }
        return false;
    }

    /**
     * @return bool|MysqlSession
     */
    public static function component()
    {
        if (property_exists(get_called_class(), 'instance')) {
            if (static::$instance instanceof static) {
                return static::$instance;
            } else {
                $new_mysql_session = new static();
                static::$instance = $new_mysql_session;
                return static::$instance;
            }
        }
        return false;
    }
}
