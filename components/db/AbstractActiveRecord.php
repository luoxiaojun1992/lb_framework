<?php

namespace lb\components\db;

use lb\BaseClass;
use lb\components\db\mysql\ActiveRecord as MysqlActiveRecord;
use lb\components\db\mongodb\ActiveRecord as MongodbActiveRecord;
use lb\components\traits\Singleton;

/**
 * Class AbstractActiveRecord
 * @package lb\components\db
 */
abstract class AbstractActiveRecord extends BaseClass
{
    use Singleton;

    protected $_attributes = [];
    protected $is_single = false;
    protected $rules = [];
    protected $errors = [];
    public $labels = [];
    public $is_new_record = true;

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        if (!$this->is_single) {
            if (array_key_exists($name, $this->_attributes)) {
                settype($value, gettype($this->_attributes[$name]));
                $this->_attributes[$name] = $value;
            }
        }
    }

    /**
     * @param $name
     * @return bool|mixed
     */
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
     * @return bool|AbstractActiveRecord|MysqlActiveRecord|MongodbActiveRecord
     */
    public static function model()
    {
        if (property_exists(get_called_class(), 'instance')) {
            if (static::$instance instanceof static) {
                return static::$instance;
            } else {
                $new_model = new static();
                $new_model->is_new_record = false;
                $new_model->is_single = true;
                static::$instance = $new_model;
                return static::$instance;
            }
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isNewRecord()
    {
        if (!$this->is_single) {
            return $this->is_new_record;
        }
        return false;
    }

    /**
     * @return int|mixed
     */
    public function getPrimaryKey()
    {
        if (!$this->is_single) {
            if (array_key_exists($this->_primary_key, $this->_attributes)) {
                return $this->_attributes[$this->_primary_key];
            }
        }
        return 0;
    }

    /**
     * @return bool|mixed
     */
    public function getPrimaryName()
    {
        return $this->_primary_key;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        if (!$this->is_single) {
            return $this->_attributes;
        }
        return [];
    }

    /**
     * @return array
     */
    public function getFields()
    {
        if (!$this->is_single) {
            return array_keys($this->_attributes);
        }
        return [];
    }

    /**
     * @return array
     */
    public function getLabels()
    {
        if (!$this->is_single) {
            return $this->labels;
        }
        return [];
    }

    /**
     * @return array|bool
     */
    public function getErrors()
    {
        if (!$this->is_single) {
            return $this->errors;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function delete()
    {
        if (!$this->is_single) {
            self::model()->deleteByPk($this->getPrimaryKey());
        }

        return false;
    }

    /**
     * @return bool
     */
    protected function beforeSave()
    {
        if (!$this->is_single) {
            if ($this->isNewRecord()) {
                $this->beforeCreate();
            } else {
                $this->beforeUpdate();
            }

            return $this->valid();
        }

        return false;
    }

    /**
     * @return bool
     */
    protected function beforeCreate()
    {
        if (!$this->is_single) {
            //
        }

        return false;
    }

    /**
     * @return bool
     */
    protected function beforeUpdate()
    {
        if (!$this->is_single) {
            //
        }

        return false;
    }

    protected function afterSave()
    {
        if (!$this->is_single) {
            //
        }
    }

    /**
     * @return bool
     */
    abstract protected function valid();

    /**
     * @return array|bool|mixed
     */
    abstract protected function save();

    /**
     * @return array|bool|mixed
     */
    abstract public function deleteByPk($primary_key);

    /**
     * @param array $attributes
     * @return $this|null
     */
    abstract public function setAttributes($attributes = []);
}
