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

    public function __set($name, $value)
    {
        if (array_key_exists($name, $this->_attributes)) {
            $this->_attributes[$name] = $value;
        }
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->_attributes)) {
            return $this->_attributes[$name];
        }
        return false;
    }

    public function setAttributes($attributes = [])
    {
        foreach ($attributes as $attribute_name => $attribute_value) {
            if (array_key_exists($attribute_name, $this->_attributes)) {
                $this->_attributes[$attribute_name] = $attribute_value;
            }
        }
    }

    public static function model()
    {
        if (property_exists(get_called_class(), '_instance')) {
            if (static::$_instance instanceof static) {
                return static::$_instance;
            } else {
                return (static::$_instance = new static());
            }
        }
        return false;
    }

    public function findAll()
    {
        $result = Dao::component()->select(['*'])->from(static::TABLE_NAME)->findAll();
        if ($result) {
            $models = [];
            foreach ($result as $attributes) {
                $model_class = get_class($this);
                $model = new $model_class();
                $model->setAttributes($attributes);
                $models[] = $model;
            }
            return $models;
        }
        return [];
    }

    public function findByPk($primary_key)
    {
        $attributes = Dao::component()
            ->select(['*'])
            ->from(static::TABLE_NAME)
            ->where([$this->_primary_key => $primary_key])
            ->find();
        if ($attributes) {
            $model_class = get_class($this);
            $model = new $model_class();
            $model->setAttributes($attributes);
            return $model;
        }
        return false;
    }

    public function getPrimaryKey()
    {
        if (array_key_exists($this->_primary_key, $this->_attributes)) {
            return $this->_attributes[$this->_primary_key];
        }
        return 0;
    }

    public function getAttributes()
    {
        return $this->_attributes;
    }
}
