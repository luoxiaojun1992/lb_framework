<?php

namespace lb\components\db\mongodb;

/**
 * Class DynamicModel
 * @package lb\components\db\mongodb
 */
class DynamicModel extends ActiveRecord
{
    public $table_name = '';

    /**
     * DynamicModel constructor.
     * @param string $table_name
     * @param array $attributes
     * @param array $labels
     */
    public function __construct($table_name = '', $attributes = [], $labels = [])
    {
        if ($table_name) {
            $this->defineTableName($table_name);
        }
        if ($attributes) {
            $this->defineAttributes($attributes);
        }
        if ($labels) {
            $this->defineLabels($labels);
        }
    }

    /**
     * @param $table_name
     */
    public function defineTableName($table_name)
    {
        $this->table_name = $table_name;
    }


    public function undefineTableName()
    {
        $this->table_name = '';
    }

    /**
     * @param $attribute
     * @param $value
     */
    public function defineAttribute($attribute, $value)
    {
        $this->_attributes[$attribute] = $value;
    }

    /**
     * @param $attribute
     */
    public function undefineAttribute($attribute)
    {
        if (isset($this->_attributes[$attribute])) {
            unset($this->_attributes[$attribute]);
        }
    }

    /**
     * @param $attributes
     */
    public function defineAttributes($attributes)
    {
        foreach ($attributes as $attribute => $value) {
            $this->defineAttribute($attribute, $value);
        }
    }

    /**
     * @param $attributes
     */
    public function undefineAttributes($attributes)
    {
        foreach ($attributes as $attribute) {
            $this->undefineAttribute($attribute);
        }
    }

    /**
     * @param $attribute
     * @param $label
     */
    public function defineLabel($attribute, $label)
    {
        $this->labels[$attribute] = $label;
    }

    /**
     * @param $attribute
     */
    public function undefineLabel($attribute)
    {
        if (isset($this->labels[$attribute])) {
            unset($this->labels[$attribute]);
        }
    }

    /**
     * @param $labels
     */
    public function defineLabels($labels)
    {
        foreach ($labels as $attribute => $label) {
            $this->defineLabel($attribute, $label);
        }
    }

    /**
     * @param $attributes
     */
    public function undefineLabels($attributes)
    {
        foreach ($attributes as $attribute) {
            $this->undefineLabel($attribute);
        }
    }
}
