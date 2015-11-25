<?php
/**
 * Created by PhpStorm.
 * User: 224
 * Date: 2015/11/12
 * Time: 17:40
 * Lb framework mysql db active record file
 */

namespace lb\components\db\mysql;

class ActiveRecord
{
    protected $_primary_key = '';
    protected $_attributes = [];
    protected $is_single = false;
    protected $rules = [];
    protected $errors = [];
    public $labels = [];
    public $is_new_record = true;

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

    public function setAttributes($attributes = [])
    {
        if (!$this->is_single) {
            foreach ($attributes as $attribute_name => $attribute_value) {
                if (array_key_exists($attribute_name, $this->_attributes)) {
                    settype($attribute_value, gettype($this->_attributes[$attribute_name]));
                    $this->_attributes[$attribute_name] = $attribute_value;
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
            $result = Dao::component()->select(['*'])->from(static::TABLE_NAME)->findAll();
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
            $attributes = Dao::component()
                ->select(['*'])
                ->from(static::TABLE_NAME)
                ->where([$this->_primary_key => $primary_key])
                ->find();
            if ($attributes) {
                $model_class = get_class($this);
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
            $result = $dao->findAll();
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

    public function getAttributes()
    {
        if (!$this->is_single) {
            return $this->_attributes;
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

    protected function valid()
    {
        $is_valid = true;
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
