<?php
/**
 * Created by PhpStorm.
 * User: 224
 * Date: 2015/11/12
 * Time: 17:40
 * Lb framework mysql db active record file
 */

namespace lb\components\db\mysql;

use lb\BaseClass;
use lb\components\helpers\ArrayHelper;
use lb\components\helpers\ValidationHelper;

class ActiveRecord extends BaseClass
{
    protected $_primary_key = '';
    protected $_attributes = [];
    protected $is_single = false;
    protected $rules = [];
    protected $errors = [];
    protected $relations = [];
    public $labels = [];
    public $is_new_record = true;

    protected static $_instance = false;

    public function __clone()
    {
        // TODO: Implement __clone() method.
    }

    public function __set($name, $value)
    {
        if (!$this->is_single) {
            if (array_key_exists($name, $this->_attributes)) {
                settype($value, gettype($this->_attributes[$name]));
                $this->_attributes[$name] = $value;
            }
        }
    }

    public function __get($name)
    {
        if (!$this->is_single) {
            if (array_key_exists($name, $this->_attributes)) {
                return $this->_attributes[$name];
            }
        }
        return false;
    }

    /**
     * @param array $attributes
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
        }
    }

    public static function model()
    {
        if (property_exists(get_called_class(), '_instance')) {
            if (static::$_instance instanceof static) {
                return static::$_instance;
            } else {
                $new_model = new static();
                $new_model->is_new_record = false;
                $new_model->is_single = true;
                static::$_instance = $new_model;
                return static::$_instance;
            }
        }
        return false;
    }

    public function findAll()
    {
        if ($this->is_single) {
            $dao = Dao::component()->select(['*'])
                ->from(static::TABLE_NAME);

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

            $result = $dao->findAll();
            if ($result) {
                $models = [];
                foreach ($result as $attributes) {
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
                    $models[] = $model;
                }
                return $models;
            }
        }
        return [];
    }

    public function countAll()
    {
        if ($this->is_single) {
            return Dao::component()->select(['*'])->from(static::TABLE_NAME)->count();
        }
        return 0;
    }

    public function findByPk($primary_key)
    {
        if ($this->is_single) {
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
            if ($attributes) {
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

    public function deleteByPk($primary_key)
    {
        if ($this->is_single) {
            return Dao::component()
                ->delete(static::TABLE_NAME, [
                    $this->_primary_key => $primary_key,
                ]);
        }
        return false;
    }

    public function deleteByConditions($conditions)
    {
        if ($this->is_single) {
            return Dao::component()
                ->delete(static::TABLE_NAME, $conditions);
        }
        return false;
    }

    public function deleteBySql($sql)
    {
        if ($this->is_single) {
            $res = false;
            $statement = Dao::component()->prepare($sql, 'master');
            if ($statement) {
                $res = $statement->execute();
            }
            return $res;
        }
        return false;
    }

    public function findByConditions($conditions = [], $group_fields = [], $orders = [], $limit = '')
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

            $result = $dao->findAll();
            if ($result) {
                $models = [];
                foreach ($result as $attributes) {
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
                    $models[] = $model;
                }
                return $models;
            }
        }
        return [];
    }

    public function findBySql($sql)
    {
        if ($this->is_single) {
            $statement = Dao::component()->prepare($sql, 'slave');
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
                        return $models;
                    }
                }
            }
        }
        return [];
    }

    public function getPrimaryKey()
    {
        if (!$this->is_single) {
            if (array_key_exists($this->_primary_key, $this->_attributes)) {
                return $this->_attributes[$this->_primary_key];
            }
        }
        return 0;
    }

    public function getPrimaryName()
    {
        return $this->_primary_key;
    }

    public function getAttributes()
    {
        if (!$this->is_single) {
            return $this->_attributes;
        }
        return [];
    }

    public function getFields()
    {
        if (!$this->is_single) {
            return array_keys($this->_attributes);
        }
        return [];
    }

    public function getLabels()
    {
        if (!$this->is_single) {
            return $this->labels;
        }
        return [];
    }

    public function isNewRecord()
    {
        if (!$this->is_single) {
            return $this->is_new_record;
        }
        return false;
    }

    public function getErrors()
    {
        if (!$this->is_single) {
            return $this->errors;
        }
        return false;
    }

    protected function valid()
    {
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
                                    if (static::findByConditions([$attribute => $attribute_value])) {
                                        $is_valid = false;
                                        $this->errors[] = "The {$attribute} is not unique.";
                                    }
                                } else {
                                    if (static::findByConditions([$attribute => $attribute_value, $this->getPrimaryName() => ['!=' => $this->getPrimaryKey()]])) {
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
        return $is_valid;
    }

    protected function beforeSave()
    {
        if (!$this->is_single) {
            return $this->valid();
        }
        return false;
    }

    protected function afterSave()
    {
        if (!$this->is_single) {
            // TODO
        }
    }

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
                    $res = Dao::component()->update(static::TABLE_NAME, $values, [$this->_primary_key => $primary_key]);
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
