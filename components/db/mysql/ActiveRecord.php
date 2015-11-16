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
    public $is_new_record = true;

    public function __set($name, $value)
    {
        if (!$this->is_single) {
            if (array_key_exists($name, $this->_attributes)) {
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

    protected function beforeSave()
    {

    }

    protected function afterSave()
    {

    }

    public function save()
    {
        if (!$this->is_single) {
            $this->beforeSave();
            if ($this->is_new_record) {
                $res = Dao::component()->insertOne(static::TABLE_NAME, array_keys($this->_attributes), array_values($this->_attributes));
                if ($res) {
                    $this->is_new_record = false;
                }
            } else {
                $res = Dao::component()->update(static::TABLE_NAME, $this->_attributes, [$this->_primary_key => $this->_attributes[$this->_primary_key]]);
            }
            if ($res) {
                $this->afterSave();
            }
        }
    }
}
