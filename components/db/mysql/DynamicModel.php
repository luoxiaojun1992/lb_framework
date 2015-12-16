<?php
/**
 * Created by PhpStorm.
 * User: 224
 * Date: 2015/12/16
 * Time: 9:48
 * Lb framework mysql db dynamic model file
 */

namespace lb\components\db\mysql;

class DynamicModel extends ActiveRecord
{
    protected $table_name = '';

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

    public function defineTableName($table_name)
    {
        $this->table_name = $table_name;
    }

    public function defineAttribute($attribute, $value)
    {
        $this->_attributes[$attribute] = $value;
    }

    public function undefineAttribute($attribute)
    {
        if (isset($this->_attributes[$attribute])) {
            unset($this->_attributes[$attribute]);
        }
    }

    public function defineAttributes($attributes)
    {
        foreach ($attributes as $attribute => $value) {
            $this->defineAttribute($attribute, $value);
        }
    }

    public function undefineAttributes($attributes)
    {
        foreach ($attributes as $attribute) {
            $this->undefineAttribute($attribute);
        }
    }

    public function defineLabel($attribute, $label)
    {
        $this->labels[$attribute] = $label;
    }

    public function undefineLabel($attribute)
    {
        if (isset($this->labels[$attribute])) {
            unset($this->labels[$attribute]);
        }
    }

    public function defineLabels($labels)
    {
        foreach ($labels as $attribute => $label) {
            $this->defineLabel($attribute, $label);
        }
    }

    public function undefineLabels($attributes)
    {
        foreach ($attributes as $attribute) {
            $this->undefineLabel($attribute);
        }
    }
}
