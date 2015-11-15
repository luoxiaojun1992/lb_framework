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
    const TABLE_NAME = '';
    protected $_attributes = [];
    protected static $_instance = false;

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
        if (property_exists('static', '_instance')) {
            if (static::$_instance instanceof static) {
                return static::$_instance;
            } else {
                return (static::$_instance = new static());
            }
        }
        return false;
    }
}
