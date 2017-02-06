<?php
/**
 * Created by PhpStorm.
 * User: 224
 * Date: 2015/11/11
 * Time: 10:35
 * Lb framework mysql db dao file
 */

namespace lb\components\db\mysql;

use lb\BaseClass;
use lb\components\helpers\ArrayHelper;
use lb\Lb;
use Monolog\Logger;

class Dao extends BaseClass
{
    protected static $instance;
    protected $_table = '';
    protected $_fields = [];
    protected $_conditions = [];
    protected $is_query = false;
    protected $is_lock_for_update = false;

    protected $_orders = [];
    protected $_limit = '';
    protected $_group_fields = [];

    protected $_joined_table = '';
    protected $_join_condition = [];
    protected $_join_type = 'LEFT';

    // Create
    const INSERT_INTO_SQL_TPL = "INSERT INTO %s (%s) VALUES (%s)";
    const MULTI_INSERT_INTO_SQL_TPL = "INSERT INTO %s (%s) VALUES %s";

    // Read
    const SELECT_FROM_SQL_TPL = "SELECT %s FROM %s";
    const SELECT_COUNT_FROM_SQL_TPL = "SELECT COUNT(*) AS total FROM %s";
    const WHERE_SQL_TPL = "WHERE %s";
    const GROUP_SQL_TPL = "GROUP BY %s";
    const ORDER_SQL_TPL = "ORDER BY %s";
    const LIMIT_SQL_TPL = "LIMIT %s";
    const JOIN_SQL_TPL = "%s JOIN %s ON %s";

    // Update
    const UPDATE_SQL_TPL = "UPDATE %s SET %s WHERE %s";

    // Delete
    const DELETE_SQL_TPL = "DELETE FROM %s WHERE %s";

    /**
     * @return Dao
     */
    public static function component()
    {
        if (static::$instance instanceof static) {
            $instance = static::$instance;
            $instance->_table = '';
            $instance->_fields = [];
            $instance->_conditions = [];
            $instance->is_query = false;
            $instance->_orders = [];
            $instance->_limit = '';
            $instance->_group_fields = [];
            $instance->_joined_table = '';
            $instance->_join_condition = [];
            $instance->_join_type = 'LEFT';
            return $instance;
        } else {
            return (static::$instance = new static());
        }
    }

    private function __construct()
    {

    }

    public function __clone()
    {
        // TODO: Implement __clone() method.
    }

    /**
     * @param $fields
     * @return bool
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
     * @return bool
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
     * @return bool
     */
    public function from($table)
    {
        $this->_table = $table;
        return static::$instance;
    }

    /**
     * @param $conditions
     * @return bool
     */
    public function where($conditions)
    {
        if ($this->_table && is_array($conditions) && $conditions) {
            $this->_conditions = $conditions;
            return static::$instance;
        }
        return false;
    }

    /**
     * @param $orders
     * @return bool
     */
    public function order($orders)
    {
        if ($this->_table && is_array($orders) && $orders) {
            $this->_orders = $orders;
            return static::$instance;
        }
        return false;
    }

    /**
     * @param $limit
     * @return bool
     */
    public function limit($limit)
    {
        if ($this->_table && $limit) {
            $this->_limit = $limit;
            return static::$instance;
        }
        return false;
    }

    /**
     * @param $group_fields
     * @return bool
     */
    public function group($group_fields)
    {
        if ($this->_table && is_array($group_fields) && $group_fields) {
            $this->_group_fields = $group_fields;
            return static::$instance;
        }
        return false;
    }

    /**
     * @param $joined_table
     * @param $condition
     * @param string $type
     * @return bool
     */
    public function join($joined_table, $condition, $type = 'LEFT')
    {
        if ($this->_table && $joined_table && is_array($condition) && $condition) {
            $this->_joined_table = $joined_table;
            $this->_join_condition = $condition;
            $this->_join_type = $type;
            return static::$instance;
        }
        return false;
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
     * @param bool $count
     * @return bool
     */
    protected function query($count = false)
    {
        $result = false;
        if ($this->is_query) {
            $query_sql_statement = $this->createQueryStatement($count);
            if ($query_sql_statement) {
                $statement = static::prepare($query_sql_statement, 'slave');
                if ($statement) {
                    try {
                        $res = $statement->execute();
                        if ($res) {
                            $result = $statement;
                        }
                    } catch(\PDOException $e) {
                        if($e->errorInfo[0] == 70100 || $e->errorInfo[0] == 2006){
                            Connection::component(Connection::component()->containers, true);
                            $statement = static::prepare($query_sql_statement, 'slave');
                            if ($statement) {
                                $res = $statement->execute();
                                if ($res) {
                                    $result = $statement;
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
     * @param $sql_statement
     * @param $node_type
     * @return bool
     */
    public static function prepare($sql_statement, $node_type)
    {
        $connection_component = Connection::component();
        $statement = false;
        switch ($node_type) {
            case 'master':
                $conn = $connection_component->write_conn;
                break;
            case 'slave':
                $conn = $connection_component->read_conn ? : $connection_component->write_conn;
                break;
            default:
                $conn = false;
        }
        if ($conn) {
            $statement = $conn->prepare($sql_statement);
        }
        return $statement;
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
                $statement = static::prepare($insert_sql_statement, 'master');
                if ($statement) {
                    try {
                        $result = $statement->execute();
                    } catch(\PDOException $e) {
                        if ($e->errorInfo[0] == 70100 || $e->errorInfo[0] == 2006) {
                            Connection::component(Connection::component()->containers, true);
                            $statement = static::prepare($insert_sql_statement, 'master');
                            if ($statement) {
                                $result = $statement->execute();
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
                $statement = static::prepare($insert_sql_statement, 'master');
                if ($statement) {
                    try {
                        $result = $statement->execute();
                    } catch(\PDOException $e) {
                        if ($e->errorInfo[0] == 70100 || $e->errorInfo[0] == 2006) {
                            Connection::component(Connection::component()->containers, true);
                            $statement = static::prepare($insert_sql_statement, 'master');
                            if ($statement) {
                                $result = $statement->execute();
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
     * @param bool $conditions
     * @return bool
     */
    public function update($table, $values, $conditions = true)
    {
        $result = false;
        if ($table && is_array($values) && $values) {
            $this->is_query = false;
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
            if (is_array($conditions)) {
                $new_conditions = [];
                foreach ($conditions as $key => $value) {
                    if (is_string($value)) {
                        $new_conditions[] = implode('=', [$key, '"' . $value . '"']);
                    } else {
                        $new_conditions[] = implode('=', [$key, $value]);
                    }
                }
                if (!$new_conditions) {
                    $new_conditions = true;
                }
            } else {
                $new_conditions = $conditions;
            }

            if ($new_values && $new_conditions) {
                $update_sql_statement = sprintf(static::UPDATE_SQL_TPL, $table, implode(',', $new_values), is_array($new_conditions) ? implode(',', $new_conditions) : $new_conditions);
                $statement = static::prepare($update_sql_statement, 'master');
                if ($statement) {
                    try {
                        $result = $statement->execute();
                    } catch(\PDOException $e) {
                        if ($e->errorInfo[0] == 70100 || $e->errorInfo[0] == 2006) {
                            Connection::component(Connection::component()->containers, true);
                            $statement = static::prepare($update_sql_statement, 'master');
                            if ($statement) {
                                $result = $statement->execute();
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
     * @param bool $conditions
     * @return bool
     */
    public function delete($table, $conditions = true)
    {
        $result = false;
        if ($table) {
            $this->is_query = false;
            if (is_array($conditions)) {
                $new_conditions = [];
                foreach ($conditions as $key => $value) {
                    if (is_string($value)) {
                        $new_conditions[] = implode('=', [$key, '"' . $value . '"']);
                    } else {
                        $new_conditions[] = implode('=', [$key, $value]);
                    }
                }
                if (!$new_conditions) {
                    $new_conditions = true;
                }
            } else {
                $new_conditions = $conditions;
            }

            if ($new_conditions) {
                $delete_sql_statement = sprintf(static::DELETE_SQL_TPL, $table, is_array($new_conditions) ? implode(',', $new_conditions) : $new_conditions);
                $statement = static::prepare($delete_sql_statement, 'master');
                if ($statement) {
                    try {
                        $result = $statement->execute();
                    } catch(\PDOException $e) {
                        if ($e->errorInfo[0] == 70100 || $e->errorInfo[0] == 2006) {
                            Connection::component(Connection::component()->containers, true);
                            $statement = static::prepare($delete_sql_statement, 'master');
                            if ($statement) {
                                $result = $statement->execute();
                            }
                        }
                    }
                }
            }
        }
        return $result;
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
                if ($this->_joined_table && $this->_join_condition) {
                    $join_conditions = [];
                    foreach ($this->_join_condition as $key => $value) {
                        $join_conditions[] = implode('=', [$key, $value]);
                    }
                    $condition_str = implode(' AND ', $join_conditions);
                    $statement .= (' ' . sprintf(static::JOIN_SQL_TPL, $this->_join_type, $this->_joined_table, $condition_str));
                }

                // WHERE
                if ($this->_conditions) {
                    $conditions = [];
                    foreach ($this->_conditions as $key => $val) {
                        if (!is_array($val)) {
                            $conditions[] = implode('=', [$key, '"' . $val . '"']);
                        } else {
                            foreach ($val as $op => $value) {
                                $conditions[] = implode(' ' . $op . ' ', [$key, $value]);
                            }
                        }
                    }
                    if ($conditions) {
                        $condition_statement = implode(' AND ', $conditions);
                        $where_sql_statement = sprintf(static::WHERE_SQL_TPL, $condition_statement);
                        $statement .= (' ' . $where_sql_statement);
                    }
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
        Lb::app()->log('system', Logger::NOTICE, 'sql:'.$statement);
        return $statement;
    }

    public static function beginTransaction()
    {
        $write_conn = Connection::component()->write_conn;
        $write_conn->setAttribute(\PDO::ATTR_AUTOCOMMIT, false);
        $write_conn->beginTransaction();
    }

    public static function commit()
    {
        $write_conn = Connection::component()->write_conn;
        $write_conn->commit();
        $write_conn->setAttribute(\PDO::ATTR_AUTOCOMMIT, true);
    }

    public static function rollBack()
    {
        $write_conn = Connection::component()->write_conn;
        $write_conn->rollBack();
        $write_conn->setAttribute(\PDO::ATTR_AUTOCOMMIT, true);
    }
}
