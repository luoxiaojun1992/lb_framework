<?php
/**
 * Created by PhpStorm.
 * User: 224
 * Date: 2015/11/11
 * Time: 10:35
 * Lb framework mysql db dao file
 */

namespace lb\components\db\mysql;

use lb\components\helpers\ArrayHelper;
use lb\components\Security;

class Dao
{
    protected static $instance = false;
    protected $_table = '';
    protected $_fields = [];
    protected $_conditions = [];
    protected $is_query = false;

    protected $_orders = [];
    protected $_limit = '';
    protected $_group_fields = [];

    // Create
    const INSERT_INTO_SQL_TPL = "INSERT INTO %s (%s) VALUES (%s)";
    const MULTI_INSERT_INTO_SQL_TPL = "INSERT INTO %s (%s) VALUES %s";

    // Read
    const SELECT_FROM_SQL_TPL = "SELECT %s FROM %s";
    const WHERE_SQL_TPL = "WHERE %s";
    const ORDER_SQL_TPL = "ORDER BY %s";
    const LIMIT_SQL_TPL = "LIMIT %s";
    const GROUP_SQL_TPL = "GROUP BY %s";

    public static function component()
    {
        if (self::$instance instanceof self) {
            return self::$instance;
        } else {
            return (self::$instance = new self());
        }
    }

    public function select($fields)
    {
        $this->is_query = true;
        if (is_array($fields) && $fields) {
            $this->_fields = $fields;
            return self::$instance;
        }
        return false;
    }

    public function from($table)
    {
        $this->_table = $table;
        return self::$instance;
    }

    public function where($conditions)
    {
        if ($this->_table && is_array($conditions) && $conditions) {
            $this->_conditions = $conditions;
            return self::$instance;
        }
        return false;
    }

    public function order($orders)
    {
        if ($this->_table && is_array($orders) && $orders) {
            $this->_orders = $orders;
            return self::$instance;
        }
        return false;
    }

    public function limit($limit)
    {
        if ($this->_table && $limit) {
            $this->_limit = $limit;
            return self::$instance;
        }
        return false;
    }

    public function group($group_fields)
    {
        if ($this->_table && is_array($group_fields) && $group_fields) {
            $this->_group_fields = $group_fields;
            return self::$instance;
        }
        return false;
    }

    public function find()
    {
        $result = [];
        $query_result = $this->query();
        if ($query_result) {
            $result = $query_result->fetch();
        }
        return $result;
    }

    public function findAll()
    {
        $result = [];
        $query_result = $this->query();
        if ($query_result) {
            $result = $query_result->fetchAll();
        }
        return $result;
    }

    protected function query()
    {
        $result = false;
        if ($this->is_query) {
            $query_sql_statement = $this->createQueryStatement();
            if ($query_sql_statement) {
                $conn = Connection::component()->conn;
                if ($conn) {
                    $statement = $conn->prepare($query_sql_statement);
                    $res = $statement->execute();
                    if ($res) {
                        $result = $statement;
                    }
                }
            }
        }
        return $result;
    }

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
                $insert_sql_statement = sprintf(self::INSERT_INTO_SQL_TPL, $table, implode(',', $fields), implode(',', $filtered_values));
                $conn = Connection::component()->conn;
                if ($conn) {
                    $statement = $conn->prepare($insert_sql_statement);
                    $result = $statement->execute();
                }
            }
        }
        return $result;
    }

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
                $insert_sql_statement = sprintf(self::MULTI_INSERT_INTO_SQL_TPL, $table, implode(',', $fields), implode(',', $filtered_multi_values));
                $conn = Connection::component()->conn;
                if ($conn) {
                    $statement = $conn->prepare($insert_sql_statement);
                    $result = $statement->execute();
                }
            }
        }
        return $result;
    }

    protected function createQueryStatement()
    {
        $statement = '';
        if ($this->is_query) {
            if ($this->_fields && $this->_table) {
                $select_from_sql_statement = sprintf(self::SELECT_FROM_SQL_TPL, implode(', ', $this->_fields), $this->_table);
                $statement .= $select_from_sql_statement;

                // WHERE
                if ($this->_conditions) {
                    $conditions = [];
                    foreach ($this->_conditions as $key => $val) {
                        if (!is_array($val)) {
                            if (is_string($val)) {
                                $conditions[] = implode('=', [$key, '"' . $val . '"']);
                            } else {
                                $conditions[] = implode('=', [$key, $val]);
                            }
                        } else {
                            foreach ($val as $op => $value) {
                                if (is_string($value)) {
                                    $conditions[] = implode($op, [$key, '"' . $value . '"']);
                                } else {
                                    $conditions[] = implode($op, [$key, $value]);
                                }
                            }
                        }
                    }
                    if ($conditions) {
                        $condition_statement = implode(' AND ', $conditions);
                        $where_sql_statement = sprintf(self::WHERE_SQL_TPL, $condition_statement);
                        $statement .= (' ' . $where_sql_statement);
                    }
                }

                // GROUP
                if ($this->_group_fields) {
                    $group_sql_statement = sprintf(self::GROUP_SQL_TPL, implode(',', $this->_group_fields));
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
                        $order_sql_statement = sprintf(self::ORDER_SQL_TPL, $order_statement);
                        $statement .= (' ' . $order_sql_statement);
                    }
                }

                // LIMIT
                if ($this->_limit) {
                    $limit_sql_statement = sprintf(self::LIMIT_SQL_TPL, $this->_limit);
                    $statement .= (' ' . $limit_sql_statement);
                }
            }
        }
        return $statement;
    }
}
