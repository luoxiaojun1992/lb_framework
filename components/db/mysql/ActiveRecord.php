<?php

namespace lb\components\db\mysql;

use lb\components\db\AbstractActiveRecord;
use lb\components\helpers\ArrayHelper;
use lb\components\helpers\ValidationHelper;

/**
 * Class ActiveRecord
 * @inheritdoc
 * @package lb\components\db\mysql
 */
class ActiveRecord extends AbstractActiveRecord
{
    const PLUS_NOTIFICATION = '+';
    const MINUS_NOTIFICATION = '-';

    protected $_primary_key = '';
    protected $relations = [];

    /**
     * @param array $attributes
     * @return $this|null
     */
    public function setAttributes($attributes = [])
    {
        if (!$this->is_single) {
            foreach ($attributes as $attribute_name => $attribute_value) {
                if (array_key_exists($attribute_name, $this->_attributes)) {
                    if (!is_object($attribute_value)) {
                        settype($attribute_value, gettype($this->_attributes[$attribute_name]));
                    }
                    $this->_attributes[$attribute_name] = $attribute_value;
                } else {
                    if ((stripos($attribute_name, static::TABLE_NAME . '_') === 0 && array_key_exists(str_replace(static::TABLE_NAME . '_', '', $attribute_name), $this->_attributes))) {
                        if (!is_object($attribute_value)) {
                            settype($attribute_value, gettype($this->_attributes[str_replace(static::TABLE_NAME . '_', '', $attribute_name)]));
                        }
                        $this->_attributes[str_replace(static::TABLE_NAME . '_', '', $attribute_name)] = $attribute_value;
                    }
                }
            }
            return $this;
        }

        return null;
    }

    /**
     * @param $isRelatedModelExists
     * @return bool|Dao|null
     */
    protected function getDaoByAll(&$isRelatedModelExists)
    {
        if ($this->is_single) {
            $dao = Dao::component()->select(['*'])
                ->from(static::TABLE_NAME);

            $isRelatedModelExists = false;
            if ($this->relations && count($this->relations) >= 3) {
                list($self_field, $joined_table, $joined_table_field) = $this->relations;
                $related_model_class = 'app\models\\' . ucfirst($joined_table);
                if (array_key_exists($self_field, $this->_attributes) && class_exists($related_model_class)) {
                    $isRelatedModelExists = true;
                    $related_model_fields = (new $related_model_class())->getFields();
                    foreach ($related_model_fields as $key => $related_model_field) {
                        $related_model_fields[$key] = implode('.', [$joined_table, $related_model_field]) . ' AS ' . implode('_', [$joined_table, $related_model_field]);
                    }
                    $self_fields = $this->getFields();
                    foreach ($self_fields as $key => $field) {
                        $self_fields[$key] = implode('.', [static::TABLE_NAME, $field]);
                    }
                    $fields = array_merge($related_model_fields, $self_fields);
                    $dao->select($fields)
                        ->join($joined_table, [$self_field => implode('.', [$joined_table, $joined_table_field])]);
                }
            }

            return $dao;
        }

        return null;
    }

    /**
     * @return array|ActiveRecord|ActiveRecord[]
     */
    public function findAll()
    {
        if ($this->is_single) {
            $result = $this->getDaoByAll($is_related_model_exists)->findAll();
            if ($result) {
                $models = [];
                foreach ($result as $attributes) {
                    $model_class = get_class($this);
                    if ($is_related_model_exists && isset($related_model_class) && isset($self_field)) {
                        /** @var ActiveRecord $related_model */
                        $related_model = new $related_model_class();
                        $related_model->setAttributes($attributes);
                        $related_model->is_new_record = false;
                        $attributes[$self_field] = $related_model;
                    }
                    /** @var ActiveRecord $model */
                    $model = new $model_class();
                    $model->setAttributes($attributes);
                    $model->is_new_record = false;
                    $models[] = $model;
                }
                return !$models || count($models) > 1 ? $models : $models[0];
            }
        }
        return [];
    }

    /**
     * @param \Closure $callback
     * @param int $limit
     */
    public function chunkAll(\Closure $callback, $limit = 10000)
    {
        if ($this->is_single) {
            $dao = $this->getDaoByAll($is_related_model_exists);
            $offset = 0;
            while($result = $dao->limit($offset . ',' . $limit)->findAll()) {
                $offset += $limit;

                $models = [];
                foreach ($result as $attributes) {
                    $model_class = get_class($this);
                    if ($is_related_model_exists && isset($related_model_class) && isset($self_field)) {
                        /** @var ActiveRecord $related_model */
                        $related_model = new $related_model_class();
                        $related_model->setAttributes($attributes);
                        $related_model->is_new_record = false;
                        $attributes[$self_field] = $related_model;
                    }
                    /** @var ActiveRecord $model */
                    $model = new $model_class();
                    $model->setAttributes($attributes);
                    $model->is_new_record = false;
                    $models[] = $model;
                }

                call_user_func_array($callback, ['result' => $models]);
            }
        }
    }

    /**
     * @param $primary_key
     * @param $expire
     * @return bool|ActiveRecord
     */
    public function findByPk($primary_key, $expire = null)
    {
        if ($this->is_single) {
            $attributes = [];
            if (method_exists($this, 'getCache')) {
                $attributes = $this->getCache(func_get_args());
            }
            if (!$attributes) {
                $dao = Dao::component()
                    ->select(['*'])
                    ->from(static::TABLE_NAME)
                    ->where([$this->_primary_key => $primary_key])
                    ->limit(1);

                $is_related_model_exists = false;
                if ($this->relations && count($this->relations) >= 3) {
                    list($self_field, $joined_table, $joined_table_field) = $this->relations;
                    $related_model_class = 'app\models\\' . ucfirst($joined_table);
                    if (array_key_exists($self_field, $this->_attributes) && class_exists($related_model_class)) {
                        $is_related_model_exists = true;
                        $related_model_fields = (new $related_model_class())->getFields();
                        foreach ($related_model_fields as $key => $related_model_field) {
                            $related_model_fields[$key] = implode('.', [$joined_table, $related_model_field]) . ' AS ' . implode('_', [$joined_table, $related_model_field]);
                        }
                        $self_fields = $this->getFields();
                        foreach ($self_fields as $key => $field) {
                            $self_fields[$key] = implode('.', [static::TABLE_NAME, $field]);
                        }
                        $fields = array_merge($related_model_fields, $self_fields);
                        $dao->select($fields)
                            ->join($joined_table, [$self_field => implode('.', [$joined_table, $joined_table_field])]);
                    }
                }

                $attributes = $dao->find();
            }
            if ($attributes) {
                if (method_exists($this, 'setCache')) {
                    $this->setCache(func_get_args(), $attributes, $expire);
                }

                $model_class = get_class($this);
                if ($is_related_model_exists && isset($related_model_class) && isset($self_field)) {
                    $related_model = new $related_model_class();
                    $related_model->setAttributes($attributes);
                    $related_model->is_new_record = false;
                    $attributes[$self_field] = $related_model;
                }
                $model = new $model_class();
                $model->setAttributes($attributes);
                $model->is_new_record = false;
                return $model;
            }
        }
        return false;
    }

    /**
     * @param $isRelatedModelExists
     * @param array $conditions
     * @param array $group_fields
     * @param array $orders
     * @param string $limit
     * @return bool|Dao|null
     */
    protected function getDaoByConditions(&$isRelatedModelExists, $conditions = [], $group_fields = [], $orders = [], $limit = '')
    {
        if ($this->is_single) {
            $dao = Dao::component()->select(['*'])->from(static::TABLE_NAME);
            if (is_array($conditions) && $conditions) {
                $dao->where($conditions);
            }
            if (is_array($group_fields) && $group_fields) {
                $dao->group($group_fields);
            }
            if (is_array($orders) && $orders) {
                $dao->order($orders);
            }
            if ($limit) {
                $dao->limit($limit);
            }

            $isRelatedModelExists = false;
            if ($this->relations && count($this->relations) >= 3) {
                list($self_field, $joined_table, $joined_table_field) = $this->relations;
                $related_model_class = 'app\models\\' . ucfirst($joined_table);
                if (array_key_exists($self_field, $this->_attributes) && class_exists($related_model_class)) {
                    $isRelatedModelExists = true;
                    $related_model_fields = (new $related_model_class())->getFields();
                    foreach ($related_model_fields as $key => $related_model_field) {
                        $related_model_fields[$key] = implode('.', [$joined_table, $related_model_field]) . ' AS ' . implode('_', [$joined_table, $related_model_field]);
                    }
                    $self_fields = $this->getFields();
                    foreach ($self_fields as $key => $field) {
                        $self_fields[$key] = implode('.', [static::TABLE_NAME, $field]);
                    }
                    $fields = array_merge($related_model_fields, $self_fields);
                    $dao->select($fields)
                        ->join($joined_table, [$self_field => implode('.', [$joined_table, $joined_table_field])]);
                }
            }

            return $dao;
        }

        return null;
    }

    /**
     * @param array $conditions
     * @param array $group_fields
     * @param array $orders
     * @param string $limit
     * @param integer $expire
     * @return array|ActiveRecord[]|ActiveRecord
     */
    public function findByConditions($conditions = [], $group_fields = [], $orders = [], $limit = '', $expire = null)
    {
        if ($this->is_single) {
            $result = [];
            if (method_exists($this, 'getCache')) {
                $result = $this->getCache(func_get_args());
            }
            if (!$result) {
                $result = $this->getDaoByConditions(
                    $is_related_model_exists,
                    $conditions,
                    $group_fields,
                    $orders,
                    $limit
                )->findAll();
            }
            if ($result) {
                if (method_exists($this, 'setCache')) {
                    $this->setCache(func_get_args(), $result, $expire);
                }

                $models = [];
                foreach ($result as $attributes) {
                    $model_class = get_class($this);
                    if ($is_related_model_exists && isset($related_model_class) && isset($self_field)) {
                        /** @var ActiveRecord $related_model */
                        $related_model = new $related_model_class();
                        $related_model->setAttributes($attributes);
                        $related_model->is_new_record = false;
                        $attributes[$self_field] = $related_model;
                    }
                    /** @var ActiveRecord $model */
                    $model = new $model_class();
                    $model->setAttributes($attributes);
                    $model->is_new_record = false;
                    $models[] = $model;
                }
                return !$models || count($models) > 1 ? $models : $models[0];
            }
        }
        return [];
    }

    /**
     * @param \Closure $callback
     * @param int $limit
     * @param array $conditions
     * @param array $group_fields
     * @param array $orders
     */
    public function chunkByConditions(
        \Closure $callback,
        $limit = 10000,
        $conditions = [],
        $group_fields = [],
        $orders = []
    )
    {
        if ($this->is_single) {
            $dao = $this->getDaoByConditions($is_related_model_exists, $conditions, $group_fields, $orders);
            $offset = 0;
            while($result = $dao->limit($offset . ',' . $limit)->findAll()) {
                $offset += $limit;

                $models = [];
                foreach ($result as $attributes) {
                    $model_class = get_class($this);
                    if ($is_related_model_exists && isset($related_model_class) && isset($self_field)) {
                        /** @var ActiveRecord $related_model */
                        $related_model = new $related_model_class();
                        $related_model->setAttributes($attributes);
                        $related_model->is_new_record = false;
                        $attributes[$self_field] = $related_model;
                    }
                    /** @var ActiveRecord $model */
                    $model = new $model_class();
                    $model->setAttributes($attributes);
                    $model->is_new_record = false;
                    $models[] = $model;
                }

                call_user_func_array($callback, ['result' => $models]);
            }
        }
    }

    /**
     * @param $sql
     * @return array|ActiveRecord[]|ActiveRecord
     */
    public function findBySql($sql)
    {
        if ($this->is_single) {
            $statement = Dao::component()->prepare($sql, Connection::CONN_TYPE_SLAVE);
            if ($statement) {
                $res = $statement->execute();
                if ($res) {
                    $result = $statement->fetchAll();
                    if ($result) {
                        $models = [];
                        foreach ($result as $attributes) {
                            $model_class = get_class($this);
                            $model = new $model_class();
                            $model->setAttributes($attributes);
                            $model->is_new_record = false;
                            $models[] = $model;
                        }
                        return !$models || count($models) > 1 ? $models : $models[0];
                    }
                }
            }
        }
        return [];
    }

    /**
     * @return int
     */
    public function countAll()
    {
        if ($this->is_single) {
            return Dao::component()->select(['*'])->from(static::TABLE_NAME)->count();
        }
        return 0;
    }

    /**
     * @param array $conditions
     * @param array $group_fields
     * @param array $orders
     * @param string $limit
     * @return int
     */
    public function countByConditions($conditions = [], $group_fields = [], $orders = [], $limit = '')
    {
        if ($this->is_single) {
            $dao = Dao::component()->select(['*'])->from(static::TABLE_NAME);
            if (is_array($conditions) && $conditions) {
                $dao->where($conditions);
            }
            if (is_array($group_fields) && $group_fields) {
                $dao->group($group_fields);
            }
            if (is_array($orders) && $orders) {
                $dao->order($orders);
            }
            if ($limit) {
                $dao->limit($limit);
            }

            return $dao->count();
        }
        return 0;
    }

    /**
     * @param $sql
     * @param $count_field
     * @return int
     */
    public function countBySql($sql, $count_field)
    {
        if ($this->is_single) {
            $statement = Dao::component()->prepare($sql, Connection::CONN_TYPE_SLAVE);
            if ($statement) {
                $res = $statement->execute();
                if ($res) {
                    $result = $statement->fetch();
                    if (isset($result[$count_field])) {
                        return $result[$count_field];
                    }
                }
            }
        }
        return 0;
    }

    /**
     * @param $primary_key
     * @param array $values
     * @return bool
     */
    public function updateByPk($primary_key, $values = [])
    {
        if ($this->is_single) {
            return Dao::component()
                ->where([
                    $this->_primary_key => $primary_key,
                ])
                ->update(static::TABLE_NAME, $values);
        }
        return false;
    }

    /**
     * Increase key or keys by primary key
     *
     * @param $primary_key
     * @param $keys
     * @param int $step
     * @return bool
     */
    public function incrementByPk($primary_key, $keys, $step = 1)
    {
        if ($this->is_single) {
            return $this->incrementOrDecrementByPk($primary_key, $keys, $step);
        }
        return false;
    }

    /**
     * Decrease key or keys by primary key
     *
     * @param $primary_key
     * @param $keys
     * @param int $step
     * @return bool
     */
    public function decrementByPk($primary_key, $keys, $step = 1)
    {
        if ($this->is_single) {
            return $this->incrementOrDecrementByPk($primary_key, $keys, $step, self::MINUS_NOTIFICATION);
        }
        return false;
    }

    /**
     * Increase or decrease key or keys by primary key
     *
     * @param $primary_key
     * @param $keys
     * @param int $steps
     * @param string $op
     * @return bool
     */
    public function incrementOrDecrementByPk($primary_key, $keys, $steps = 1, $op = self::PLUS_NOTIFICATION)
    {
        if ($this->is_single) {
            $values = [];
            if (is_array($keys)) {
                foreach ($keys as $k => $key) {
                    if (is_array($steps) && isset($steps[$k])) {
                        $values[$key] = [$op => $steps[$k]];
                    } else {
                        $values[$key] = [$op => $steps];
                    }
                }
            } else {
                $values[$keys] = [$op => $steps];
            }

            return Dao::component()
                ->where([
                    $this->_primary_key => $primary_key,
                ])
                ->update(static::TABLE_NAME, $values);
        }
        return false;
    }

    /**
     * @param $primary_key
     * @return bool
     */
    public function deleteByPk($primary_key)
    {
        if ($this->is_single) {
            return Dao::component()
                ->where([
                    $this->_primary_key => $primary_key,
                ])
                ->delete(static::TABLE_NAME);
        }
        return false;
    }

    /**
     * @param $conditions
     * @return bool
     */
    public function deleteByConditions($conditions)
    {
        if ($this->is_single) {
            return Dao::component()
                ->where($conditions)
                ->delete(static::TABLE_NAME);
        }
        return false;
    }

    /**
     * @param $sql
     * @return bool
     */
    public function deleteBySql($sql)
    {
        if ($this->is_single) {
            $res = false;
            $statement = Dao::component()->prepare($sql, Connection::CONN_TYPE_MASTER);
            if ($statement) {
                $res = $statement->execute();
            }
            return $res;
        }
        return false;
    }

    /**
     * @return bool
     */
    protected function valid()
    {
        if (!$this->beforeValid()) {
            return false;
        }

        $is_valid = true;
        $this->errors = [];
        $rules = $this->rules;
        if ($rules) {
            foreach ($rules as $rule) {
                list($attributes, $rule_type, $condition) = $rule;
                foreach ($attributes as $attribute) {
                    if (array_key_exists($attribute, $this->_attributes)) {
                        $attribute_value = $this->_attributes[$attribute];
                        switch ($rule_type) {
                            case 'length':
                                foreach ($condition as $op => $condition_value) {
                                    switch ($op) {
                                        case 'max':
                                            if (strlen($attribute_value) > $condition_value) {
                                                $is_valid = false;
                                                $this->errors[] = "The length of {$attribute} can't be more than {$condition_value}.";
                                            }
                                            break;
                                        case 'min':
                                            if (strlen($attribute_value) < $condition_value) {
                                                $is_valid = false;
                                                $this->errors[] = "The length of {$attribute} can't be less than {$condition_value}.";
                                            }
                                            break;
                                    }
                                }
                                break;
                            case 'mb_length':
                                foreach ($condition as $op => $condition_value) {
                                    switch ($op) {
                                        case 'max':
                                            if (!is_array($condition_value)) {
                                                if (mb_strlen($attribute_value, 'utf8') > $condition_value) {
                                                    $is_valid = false;
                                                    $this->errors[] = "The length of {$attribute} can't be more than {$condition_value}.";
                                                }
                                            } else {
                                                if (!ArrayHelper::is_multi_array($condition_value) && count($condition_value) >= 2) {
                                                    list($encoding, $value) = $condition_value;
                                                    if (is_string($encoding) && is_int($value)) {
                                                        if (mb_strlen($attribute_value, $encoding) > $value) {
                                                            $is_valid = false;
                                                            $this->errors[] = "The length of {$attribute} can't be more than {$value}.";
                                                        }
                                                    }
                                                }
                                            }
                                            break;
                                        case 'min':
                                            if (!is_array($condition_value)) {
                                                if (mb_strlen($attribute_value, 'utf8') < $condition_value) {
                                                    $is_valid = false;
                                                    $this->errors[] = "The length of {$attribute} can't be less than {$condition_value}.";
                                                }
                                            } else {
                                                if (!ArrayHelper::is_multi_array($condition_value) && count($condition_value) >= 2) {
                                                    list($encoding, $value) = $condition_value;
                                                    if (is_string($encoding) && is_int($value)) {
                                                        if (mb_strlen($attribute_value, $encoding) < $value) {
                                                            $is_valid = false;
                                                            $this->errors[] = "The length of {$attribute} can't be less than {$value}.";
                                                        }
                                                    }
                                                }
                                            }
                                            break;
                                    }
                                }
                                break;
                            case 'required':
                                if (!ValidationHelper::isRequired($attribute_value)) {
                                    $is_valid = false;
                                    $this->errors[] = "The {$attribute} is required.";
                                }
                                break;
                            case 'email':
                                if (!ValidationHelper::isEmail($attribute_value)) {
                                    $is_valid = false;
                                    $this->errors[] = "The {$attribute} is not a valid email.";
                                }
                                break;
                            case 'ip':
                                if (!ValidationHelper::isIP($attribute_value)) {
                                    $is_valid = false;
                                    $this->errors[] = "The {$attribute} is not a valid ip.";
                                }
                                break;
                            case 'unique':
                                if ($this->is_new_record) {
                                    if ($attribute_value && static::model()->findByConditions([$attribute => $attribute_value])) {
                                        $is_valid = false;
                                        $this->errors[] = "The {$attribute} is not unique.";
                                    }
                                } else {
                                    if ($attribute_value && static::model()->findByConditions([$attribute => $attribute_value, $this->getPrimaryName() => ['!=' => $this->getPrimaryKey()]])) {
                                        $is_valid = false;
                                        $this->errors[] = "The {$attribute} is not unique.";
                                    }
                                }
                                break;
                        }
                    }
                }
            }
        }

        $this->afterValid();

        return $is_valid;
    }

    /**
     * @return bool
     */
    public function save()
    {
        if (!$this->is_single) {
            if ($this->beforeSave()) {
                if ($this->is_new_record) {
                    $values = $this->_attributes;
                    if (array_key_exists($this->_primary_key, $this->_attributes)) {
                        $this->{$this->_primary_key} = 0;
                        unset($values[$this->_primary_key]);
                    }
                    $res = Dao::component()->insertOne(static::TABLE_NAME, array_keys($values), array_values($values));
                    if ($res) {
                        $this->{$this->_primary_key} = Connection::component()->write_conn->lastInsertId();
                        $this->is_new_record = false;
                    }
                } else {
                    $values = $this->_attributes;
                    $primary_key = 0;
                    if (array_key_exists($this->_primary_key, $this->_attributes)) {
                        $primary_key = $this->_attributes[$this->_primary_key];
                        unset($values[$this->_primary_key]);
                    }
                    $res = Dao::component()
                        ->where([$this->_primary_key => $primary_key])
                        ->update(static::TABLE_NAME, $values);
                }
                if ($res) {
                    $this->afterSave();
                }
                return $res;
            }
        }
        return false;
    }
}
