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
    protected $rules = [];
    protected $errors = [];
    protected $_primary_key = '';
    protected $relations = [];

    public $labels = [];
    public $is_new_record = true;

    const TABLE_NAME = '';

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        if (!$this->isSingle()) {
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
        if (!$this->isSingle()) {
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
        if (!$this->isSingle()) {
            return $this->is_new_record;
        }
        return false;
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return static::TABLE_NAME;
    }

    /**
     * @return array
     */
    public function getRelations()
    {
        return $this->relations;
    }

    /**
     * @return int|mixed
     */
    public function getPrimaryKey()
    {
        if (!$this->isSingle()) {
            if (array_key_exists($this->_primary_key, $this->_attributes)) {
                return $this->_attributes[$this->_primary_key];
            }
        }
        return null;
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
        if (!$this->isSingle()) {
            return $this->_attributes;
        }
        return [];
    }

    /**
     * @return array
     */
    public function getFields()
    {
        if (!$this->isSingle()) {
            return array_keys($this->_attributes);
        }
        return [];
    }

    /**
     * @return array
     */
    public function getLabels()
    {
        if (!$this->isSingle()) {
            return $this->labels;
        }
        return [];
    }

    /**
     * @return array|bool
     */
    public function getErrors()
    {
        if (!$this->isSingle()) {
            return $this->errors;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function delete()
    {
        if (!$this->isSingle()) {
            self::model()->deleteByPk($this->getPrimaryKey());
        }

        return false;
    }

    /**
     * @return bool
     */
    protected function beforeSave()
    {
        if (!$this->isSingle()) {
            if ($this->isNewRecord()) {
                if (!$this->beforeCreate()) {
                    return false;
                }
            } else {
                if (!$this->beforeUpdate()) {
                    return false;
                }
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
        if (!$this->isSingle()) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    protected function beforeUpdate()
    {
        if (!$this->isSingle()) {
            return true;
        }

        return false;
    }

    protected function afterSave()
    {
        if (!$this->isSingle()) {
            //
        }
    }

    /**
     * @return bool
     */
    protected function beforeValid()
    {
        if (!$this->isSingle()) {
            return true;
        }

        return false;
    }

    protected function afterValid()
    {
        if (!$this->isSingle()) {
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
