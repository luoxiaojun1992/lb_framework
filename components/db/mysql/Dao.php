<?php

namespace lb\components\db\mysql;

use lb\BaseClass;
use lb\components\consts\Event;
use lb\components\events\PDOEvent;
use lb\components\helpers\ArrayHelper;
use lb\components\traits\BaseObject;
use lb\Lb;

class Dao extends BaseClass
{
    use BaseObject;

    /**
     * @var  Dao 
     */
    protected static $instance;
    protected $_table = '';
    protected $_fields = [];
    protected $_conditions = [];
    protected $is_query = false;
    protected $is_lock_for_update = false;

    protected $_orders = [];
    protected $_limit = '';
    protected $_group_fields = [];

    protected $_joins = [];

    protected $_level = 0;

    /**
     * @var \PDOStatement 
     */
    protected $_statement = null;

    //Join Type
    const JOIN_TYPE_LEFT = 'LEFT';
    const JOIN_TYPE_RIGHT = 'RIGHT';
    const JOIN_TYPE_INNER = 'INNER';

    //Create
    const INSERT_INTO_SQL_TPL = "INSERT INTO %s (%s) VALUES (%s)";
    const MULTI_INSERT_INTO_SQL_TPL = "INSERT INTO %s (%s) VALUES %s";

    //Read
    const SELECT_FROM_SQL_TPL = "SELECT %s FROM %s";
    const SELECT_COUNT_FROM_SQL_TPL = "SELECT COUNT(*) AS total FROM %s";
    const WHERE_SQL_TPL = "WHERE %s";
    const GROUP_SQL_TPL = "GROUP BY %s";
    const ORDER_SQL_TPL = "ORDER BY %s";
    const LIMIT_SQL_TPL = "LIMIT %s";
    const JOIN_SQL_TPL = "%s JOIN %s ON %s";

    //Update
    const UPDATE_SQL_TPL = "UPDATE %s SET %s";

    //Delete
    const DELETE_SQL_TPL = "DELETE FROM %s";

    /**
     * @return Dao
     */
    public static function component()
    {
        if (static::$instance instanceof static) {
            $instance = static::$instance;
            $instance->setProperties(
                [
                    '_table' => '',
                    '_fields' => [],
                    '_conditions' => [],
                    'is_query' => false,
                    'is_lock_for_update' => false,
                    '_orders' => [],
                    '_limit' => '',
                    '_group_fields' => [],
                    '_joins' => [],
                    '_statement' => null,
                ]
            );
            return $instance;
        } else {
            return (static::$instance = new static());
        }
    }

    private function __construct()
    {
        //
    }

    public function __clone()
    {
        //
    }

    /**
     * @param $fields
     * @return bool|Dao
     */
    public function select($fields)
    {
        $this->is_query = true;
        if (is_array($fields) && $fields) {
            $this->_fields = $fields;
            return static::$instance;
        }
        return false;
    }

    /**
     * @return bool|Dao
     */
    public function lockForUpdate()
    {
        if ($this->is_query) {
            $this->is_lock_for_update = true;
            return static::$instance;
        }
        return false;
    }

    /**
     * @param $table
     * @return bool|Dao
     */
    public function from($table)
    {
        $this->_table = $table;
        return static::$instance;
    }

    /**
     * @param $conditions
     * @return bool|Dao
     */
    public function where($conditions)
    {
        if (is_array($conditions) && $conditions) {
            $this->_conditions = $conditions;
            return static::$instance;
        }
        return false;
    }

    /**
     * @param $orders
     * @return bool|Dao
     */
    public function order($orders)
    {
        if (is_array($orders) && $orders) {
            $this->_orders = $orders;
            return static::$instance;
        }
        return false;
    }

    /**
     * @param $limit
     * @return bool|Dao
     */
    public function limit($limit)
    {
        if ($limit) {
            $this->_limit = $limit;
            return static::$instance;
        }
        return false;
    }

    /**
     * @param $group_fields
     * @return bool|Dao
     */
    public function group($group_fields)
    {
        if (is_array($group_fields) && $group_fields) {
            $this->_group_fields = $group_fields;
            return static::$instance;
        }
        return false;
    }

    /**
     * @param $joined_table
     * @param $condition
     * @param string       $type
     * @return bool|Dao
     */
    public function join($joined_table, $condition, $type = self::JOIN_TYPE_LEFT)
    {
        if ($this->_table && $joined_table && is_array($condition) && $condition) {
            $this->_joins[] = [
                '_joined_table' => $joined_table,
                '_join_condition' => $condition,
                '_join_type' => $type,
            ];
            return static::$instance;
        }
        return false;
    }

    public function leftJoin($joined_table, $condition)
    {
        return $this->join($joined_table, $condition, self::JOIN_TYPE_LEFT);
    }

    public function rightJoin($joined_table, $condition)
    {
        return $this->join($joined_table, $condition, self::JOIN_TYPE_RIGHT);
    }

    public function innerJoin($joined_table, $condition)
    {
        return $this->join($joined_table, $condition, self::JOIN_TYPE_INNER);
    }

    /**
     * @return int
     */
    public function count()
    {
        $count = 0;
        $query_result = $this->query(true);
        if ($query_result) {
            $result = $query_result->fetch();
            if (isset($result['total'])) {
                $count = $result['total'];
            }
        }
        return $count;
    }

    /**
     * @return array
     */
    public function find()
    {
        $result = [];
        $query_result = $this->query();
        if ($query_result) {
            $result = $query_result->fetch();
        }
        return $result;
    }

    /**
     * @return array
     */
    public function findAll()
    {
        $result = [];
        $query_result = $this->query();
        if ($query_result) {
            $result = $query_result->fetchAll();
        }
        return $result;
    }

    /**
     * Chunk query
     *
     * @param \Closure $callBack
     * @param int      $limit
     */
    public function chunk(\Closure $callBack, $limit = 10000)
    {
        $offset = 0;
        while($result = $this->limit($offset . ',' . $limit)->findAll()) {
            $offset += $limit;
            call_user_func_array($callBack, ['result' => $result]);
        }
    }

    /**
     * @param bool $count
     * @return null|\PDOStatement
     */
    protected function query($count = false)
    {
        $result = null;
        if ($this->is_query) {
            $query_sql_statement = $this->createQueryStatement($count);
            if ($query_sql_statement) {
                $this->prepare($query_sql_statement, Connection::CONN_TYPE_SLAVE);
                if ($this->_statement) {
                    try {
                        $res = $this->execute();
                        if ($res) {
                            $result = $this->_statement;
                        }
                    } catch(\PDOException $e) {
                        if($e->errorInfo[0] == 70100 || $e->errorInfo[0] == 2006) {
                            Connection::component(Connection::component()->containers, true);
                            $this->prepare($query_sql_statement, Connection::CONN_TYPE_SLAVE);
                            if ($this->_statement) {
                                $res = $this->execute();
                                if ($res) {
                                    $result = $this->_statement;
                                }
                            }
                        }
                    }
                }
            }
        }
        return $result;
    }

    /**
     * @param $nodeType
     * @return \PDO
     */
    protected function getConnByNodeType($nodeType)
    {
        /**
 * @var Connection $connection_component 
*/
        $connection_component = Connection::component();
        switch ($nodeType) {
        case Connection::CONN_TYPE_SLAVE:
            if ($connection_component->write_conn->inTransaction()) {
                $conn = $connection_component->write_conn;
            } else {
                $conn = $connection_component->read_conn ?: $connection_component->write_conn;
            }
            break;
        case Connection::CONN_TYPE_MASTER:
        default:
            $conn = $connection_component->write_conn;
        }
        return $conn;
    }

    /**
     * @param $sql_statement
     * @param $node_type
     * @return null|\PDOStatement
     */
    public function prepare($sql_statement, $node_type)
    {
        $statement = null;
        if ($conn = $this->getConnByNodeType($node_type)) {
            $statement = $conn->prepare($sql_statement);

            //Binding values
            $this->bindValues($statement);

            $this->_statement = $statement;
        }

        return $statement;
    }

    /**
     * @param $val
     * @return int
     */
    protected function getBindType($val)
    {
        return is_string($val) ? \PDO::PARAM_STR : \PDO::PARAM_INT;
    }

    /**
     * @param \PDOStatement $statement
     */
    protected function bindValues(\PDOStatement $statement)
    {
        $i = 1;
        foreach ($this->_conditions as $val) {
            if (!is_array($val)) {
                $statement->bindValue($i++, $val, $this->getBindType($val));
            } else {
                foreach ($val as $value) {
                    $statement->bindValue($i++, $value, $this->getBindType($value));
                }
            }
        }
    }

    /**
     * @param $table
     * @param $fields
     * @param $values
     * @return bool
     */
    public function insertOne($table, $fields, $values)
    {
        $result = false;
        if ($table && is_array($fields) && is_array($values) && $fields && $values) {
            $this->is_query = false;
            $filtered_values = [];
            foreach ($values as $value) {
                if (is_string($value)) {
                    $filtered_values[] = '"' . $value . '"';
                } else {
                    $filtered_values[] = $value;
                }
            }
            if ($filtered_values) {
                $insert_sql_statement = sprintf(static::INSERT_INTO_SQL_TPL, $table, implode(',', $fields), implode(',', $filtered_values));
                $statement = $this->prepare($insert_sql_statement, Connection::CONN_TYPE_MASTER);
                if ($statement) {
                    try {
                        $result = $this->execute();
                    } catch(\PDOException $e) {
                        if ($e->errorInfo[0] == 70100 || $e->errorInfo[0] == 2006) {
                            Connection::component(Connection::component()->containers, true);
                            $statement = $this->prepare($insert_sql_statement, Connection::CONN_TYPE_MASTER);
                            if ($statement) {
                                $result = $this->execute();
                            }
                        }
                    }
                }
            }
        }
        return $result;
    }

    /**
     * @param $table
     * @param $fields
     * @param $multi_values
     * @return bool
     */
    public function insertAll($table, $fields, $multi_values)
    {
        $result = false;
        if ($table && is_array($fields) && $fields && is_array($multi_values) && ArrayHelper::is_multi_array($multi_values)) {
            $this->is_query = false;
            $filtered_multi_values = [];
            foreach ($multi_values as $values) {
                $filtered_values = [];
                foreach ($values as $value) {
                    if (is_string($value)) {
                        $filtered_values[] = '"' . $value . '"';
                    } else {
                        $filtered_values[] = $value;
                    }
                }
                if ($filtered_values) {
                    $filtered_multi_values[] = '(' . implode(',', $filtered_values) . ')';
                }
            }
            if ($filtered_multi_values) {
                $insert_sql_statement = sprintf(static::MULTI_INSERT_INTO_SQL_TPL, $table, implode(',', $fields), implode(',', $filtered_multi_values));
                $statement = $this->prepare($insert_sql_statement, Connection::CONN_TYPE_MASTER);
                if ($statement) {
                    try {
                        $result = $this->execute();
                    } catch(\PDOException $e) {
                        if ($e->errorInfo[0] == 70100 || $e->errorInfo[0] == 2006) {
                            Connection::component(Connection::component()->containers, true);
                            $statement = $this->prepare($insert_sql_statement, Connection::CONN_TYPE_MASTER);
                            if ($statement) {
                                $result = $this->execute();
                            }
                        }
                    }
                }
            }
        }
        return $result;
    }

    /**
     * @param $table
     * @param $values
     * @return bool
     */
    public function update($table, $values)
    {
        $result = false;
        if ($table && is_array($values) && $values) {
            $this->is_query = false;

            //Assembling new values
            $new_values = [];
            foreach ($values as $key => $value) {
                if (is_string($value)) {
                    $new_values[] = implode('=', [$key, '"' . $value . '"']);
                } else {
                    if (is_array($value)) {
                        $op_value_info = each($value);
                        $new_values[] = implode(
                            '=',
                            [
                                $key,
                                implode($op_value_info['key'], [$key, $op_value_info['value']])
                            ]
                        );
                    } else {
                        $new_values[] = implode('=', [$key, $value]);
                    }
                }
            }

            if ($new_values) {
                $update_sql_statement = sprintf(static::UPDATE_SQL_TPL, $table, implode(',', $new_values));

                // WHERE
                if ($this->_conditions) {
                    $update_sql_statement .= (' ' . $this->assembleConditionStatement());
                }

                $statement = $this->prepare($update_sql_statement, Connection::CONN_TYPE_MASTER);
                if ($statement) {
                    try {
                        $result = $this->execute();
                    } catch(\PDOException $e) {
                        if ($e->errorInfo[0] == 70100 || $e->errorInfo[0] == 2006) {
                            Connection::component(Connection::component()->containers, true);
                            $statement = $this->prepare($update_sql_statement, Connection::CONN_TYPE_MASTER);
                            if ($statement) {
                                $result = $this->execute();
                            }
                        }
                    }
                }
            }
        }
        return $result;
    }

    /**
     * @param $table
     * @return bool
     */
    public function delete($table)
    {
        $result = false;
        if ($table) {
            $this->is_query = false;

            $delete_sql_statement = sprintf(static::DELETE_SQL_TPL, $table);

            // WHERE
            if ($this->_conditions) {
                $delete_sql_statement .= (' ' . $this->assembleConditionStatement());
            }

            $statement = $this->prepare($delete_sql_statement, Connection::CONN_TYPE_MASTER);
            if ($statement) {
                try {
                    $result = $this->execute();
                } catch (\PDOException $e) {
                    if ($e->errorInfo[0] == 70100 || $e->errorInfo[0] == 2006) {
                        Connection::component(Connection::component()->containers, true);
                        $statement = $this->prepare($delete_sql_statement, Connection::CONN_TYPE_MASTER);
                        if ($statement) {
                            $result = $this->execute();
                        }
                    }
                }
            }
        }
        return $result;
    }

    /**
     * @return string
     */
    protected function assembleConditionStatement()
    {
        $conditions = [];
        foreach ($this->_conditions as $key => $val) {
            if (!is_array($val)) {
                $conditions[] = implode('=', [$key, '?']);
            } else {
                foreach ($val as $op => $value) {
                    $conditions[] = implode(' ' . $op . ' ', [$key, '?']);
                }
            }
        }
        if ($conditions) {
            $condition_statement = implode(' AND ', $conditions);
            return sprintf(static::WHERE_SQL_TPL, $condition_statement);
        }

        return sprintf(static::WHERE_SQL_TPL, 'true');
    }

    /**
     * @param bool $count
     * @return string
     */
    protected function createQueryStatement($count = false)
    {
        $statement = '';
        if ($this->is_query) {
            if ($this->_fields && $this->_table) {
                if ($count) {
                    $select_from_sql_statement = sprintf(static::SELECT_COUNT_FROM_SQL_TPL, $this->_table);
                } else {
                    $select_from_sql_statement = sprintf(static::SELECT_FROM_SQL_TPL, implode(', ', $this->_fields), $this->_table);
                }
                $statement .= $select_from_sql_statement;

                // JOIN
                foreach ($this->_joins as $join) {
                    if (!empty($join['_joined_table']) && !empty($join['_join_condition']) && !empty($join['_join_type'])) {
                        $join_conditions = [];
                        foreach ($join['_join_condition'] as $key => $value) {
                            $join_conditions[] = implode('=', [$key, $value]);
                        }
                        $condition_str = implode(' AND ', $join_conditions);
                        $statement .= (' ' . sprintf(static::JOIN_SQL_TPL, $join['_join_type'], $join['_joined_table'], $condition_str));
                    }
                }

                // WHERE
                if ($this->_conditions) {
                    $statement .= (' ' . $this->assembleConditionStatement());
                }

                // Lock For Update
                if ($this->is_lock_for_update) {
                    $statement .= ' FOR UPDATE';
                }

                // GROUP
                if ($this->_group_fields) {
                    $group_sql_statement = sprintf(static::GROUP_SQL_TPL, implode(',', $this->_group_fields));
                    $statement .= (' ' . $group_sql_statement);
                }

                // ORDER
                if ($this->_orders) {
                    $orders = [];
                    foreach ($this->_orders as $key => $val) {
                        $orders[] = implode(' ', [$key, $val]);
                    }
                    if ($orders) {
                        $order_statement = implode(',', $orders);
                        $order_sql_statement = sprintf(static::ORDER_SQL_TPL, $order_statement);
                        $statement .= (' ' . $order_sql_statement);
                    }
                }

                // LIMIT
                if ($this->_limit) {
                    $limit_sql_statement = sprintf(static::LIMIT_SQL_TPL, $this->_limit);
                    $statement .= (' ' . $limit_sql_statement);
                }
            }
        }
        Lb::app()->log('sql:'.$statement);
        return $statement;
    }

    public function beginTransaction()
    {
        $write_conn = $this->getConnByNodeType(Connection::CONN_TYPE_MASTER);
        if (!$this->_level) {
            $write_conn->setAttribute(\PDO::ATTR_AUTOCOMMIT, false);
            $write_conn->beginTransaction();
        } else {
            $write_conn->exec('SAVEPOINT trans'.$this->_level);
        }
        $this->_level++;
    }

    public function commit()
    {
        $write_conn = $this->getConnByNodeType(Connection::CONN_TYPE_MASTER);
        if ($write_conn->inTransaction()) {
            $currentLevel = $this->_level - 1;
            if (!$currentLevel) {
                $write_conn->commit();
                $write_conn->setAttribute(\PDO::ATTR_AUTOCOMMIT, true);
            } else {
                $write_conn->exec('RELEASE SAVEPOINT trans' . $currentLevel);
            }
            $this->_level--;
        }
    }

    public function rollBack()
    {
        $write_conn = $this->getConnByNodeType(Connection::CONN_TYPE_MASTER);
        if ($write_conn->inTransaction()) {
            $currentLevel = $this->_level - 1;
            if (!$currentLevel) {
                $write_conn->rollBack();
                $write_conn->setAttribute(\PDO::ATTR_AUTOCOMMIT, true);
            } else {
                $write_conn->exec('ROLLBACK TO trans' . $currentLevel);
            }
            $this->_level--;
        }
    }

    public function getQuerySql()
    {
        if (!$this->_statement) {
            return '';
        }

        return $this->_statement->queryString;
    }

    public function execute()
    {
        if (!$this->_statement) {
            return false;
        }

        $start = microtime(true);
        $startMemory = memory_get_usage();

        $res = $this->_statement->execute();

        $bindings = [];
        $i = 1;
        foreach ($this->_conditions as $val) {
            if (!is_array($val)) {
                $bindings[$i++] = $val;
            } else {
                foreach ($val as $value) {
                    $bindings[$i++] = $value;
                }
            }
        }

        $pdoEvent = (new PDOEvent())->setPdoStatement($this->_statement)
            ->setStartTime($start)
            ->setEndTime(microtime(true))
            ->setDuration(microtime(true) - $start)
            ->setStartMemory($startMemory)
            ->setEndMemory(memory_get_usage())
            ->setMemory(memory_get_usage() - $startMemory)
            ->setStatement($this->getQuerySql())
            ->setBindings($bindings);
        Lb::app()->trigger(Event::PDO_EVENT, $pdoEvent);

        return $res;
    }
}
