<?php

namespace lb\components\session;

use lb\components\db\mysql\Connection;
use lb\components\db\mysql\Dao;
use lb\components\traits\Singleton;
use lb\models\SwooleSession as SwooleSessionModel;

class SwooleSession extends \SessionHandler
{
    use Singleton;

    private function __construct()
    {
        Connection::component()->write_conn->exec(
            'CREATE TABLE IF NOT EXISTS swoole_session '
            . '(id int unsigned not null primary key, expire int unsigned not null default 0, data text not null)'
        );
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
        $swooleSession = SwooleSessionModel::model()->findByPk($id);
        if ($swooleSession) {
            return $swooleSession->data;
        }
        return '';
    }

    public function write($id, $sess_data)
    {
        if ($sess_data) {
            $sess_data = addslashes($sess_data);
            $swooleSession = SwooleSessionModel::model()->findByPk($id);
            $now_time = time();
            $expire_time = $now_time + intval(get_cfg_var('session.gc_maxlifetime'));
            if ($swooleSession) {
                $swooleSession->setAttributes([
                    'expire' => $expire_time,
                    'data' => $sess_data,
                ]);
                if ($swooleSession->save()) {
                    return true;
                }
            } else {
                $sql = 'INSERT INTO `swoole_session` (`id`,`expire`,`data`) VALUES("' . $id . '", ' . $expire_time . ', "' . $sess_data . '")';
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
        if (SwooleSessionModel::model()->deleteByPk($id)) {
            return true;
        }
        return false;
    }

    public function gc($maxlifetime)
    {
        if (SwooleSessionModel::model()->deleteByConditions(['expire' => ['<' => $maxlifetime]])) {
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
