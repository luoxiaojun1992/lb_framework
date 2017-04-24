<?php

namespace lb\components\db\mongodb;

use lb\components\db\AbstractActiveRecord;
use lb\components\helpers\ArrayHelper;
use lb\components\helpers\ValidationHelper;

class ActiveRecord extends AbstractActiveRecord
{
    protected $_primary_key = '_id';

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
                }
            }

            return $this;
        }

        return null;
    }

    /**
     * @return array|ActiveRecord|ActiveRecord[]
     */
    public function findAll()
    {
        if ($this->is_single) {
            $result = Dao::component()->read(static::TABLE_NAME);
            if ($result) {
                $models = [];
                foreach ($result as $attributes) {
                    $model_class = static::className();
                    $model = new $model_class();
                    $attributes_array = [];
                    foreach ($attributes as $key => $attribute) {
                        if ($key != $this->getPrimaryName()) {
                            $attributes_array[$key] = $attribute;
                        } else {
                            $attributes_array[$key] = $attribute->__toString();
                        }
                    }
                    $model->setAttributes($attributes_array);
                    $model->is_new_record = false;
                    $models[] = $model;
                }
                return !$models || count($models) > 1 ? $models : $models[0];
            }
        }
        return [];
    }

    /**
     * @param array $conditions
     * @return array|ActiveRecord[]|ActiveRecord
     */
    public function findByConditions($conditions = [])
    {
        if ($this->is_single) {
            $result = Dao::component()->read(static::TABLE_NAME, $conditions);
            if ($result) {
                $models = [];
                foreach ($result as $attributes) {
                    $model_class = static::className();
                    $model = new $model_class();
                    $attributes_array = [];
                    foreach ($attributes as $key => $attribute) {
                        if ($key != $this->getPrimaryName()) {
                            $attributes_array[$key] = $attribute;
                        } else {
                            $attributes_array[$key] = $attribute->__toString();
                        }
                    }
                    $model->setAttributes($attributes_array);
                    $model->is_new_record = false;
                    $models[] = $model;
                }
                return !$models || count($models) > 1 ? $models : $models[0];
            }
        }
        return [];
    }

//    public function countAll()
//    {
//        if ($this->is_single) {
//            return Dao::component()->select(['*'])->from(static::TABLE_NAME)->count();
//        }
//        return 0;
//    }
//
//    public function findByPk($primary_key)
//    {
//        if ($this->is_single) {
//            $dao = Dao::component()
//                ->select(['*'])
//                ->from(static::TABLE_NAME)
//                ->where([$this->_primary_key => $primary_key])
//                ->limit(1);
//
//            $is_related_model_exists = false;
//            if ($this->relations && count($this->relations) >= 3) {
//                list($self_field, $joined_table, $joined_table_field) = $this->relations;
//                $related_model_class = 'app\models\\' . ucfirst($joined_table);
//                if (array_key_exists($self_field, $this->_attributes) && class_exists($related_model_class)) {
//                    $is_related_model_exists = true;
//                    $related_model_fields = (new $related_model_class())->getFields();
//                    foreach ($related_model_fields as $key => $related_model_field) {
//                        $related_model_fields[$key] = implode('.', [$joined_table, $related_model_field]) . ' AS ' . implode('_', [$joined_table, $related_model_field]);
//                    }
//                    $self_fields = $this->getFields();
//                    foreach ($self_fields as $key => $field) {
//                        $self_fields[$key] = implode('.', [static::TABLE_NAME, $field]);
//                    }
//                    $fields = array_merge($related_model_fields, $self_fields);
//                    $dao->select($fields)
//                        ->join($joined_table, [$self_field => implode('.', [$joined_table, $joined_table_field])]);
//                }
//            }
//
//            $attributes = $dao->find();
//            if ($attributes) {
//                $model_class = get_class($this);
//                if ($is_related_model_exists && isset($related_model_class) && isset($self_field)) {
//                    $related_model = new $related_model_class();
//                    $related_model->setAttributes($attributes);
//                    $related_model->is_new_record = false;
//                    $attributes[$self_field] = $related_model;
//                }
//                $model = new $model_class();
//                $model->setAttributes($attributes);
//                $model->is_new_record = false;
//                return $model;
//            }
//        }
//        return false;
//    }

    /**
     * @param $primary_key
     * @return bool
     */
    public function deleteByPk($primary_key)
    {
        if ($this->is_single) {
            return Dao::component()
                ->delete(static::TABLE_NAME, [
                    $this->_primary_key => new \MongoDB\BSON\ObjectID($primary_key),
                ]);
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
                ->delete(static::TABLE_NAME, $conditions, 0);
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
//                            case 'unique':
//                                if ($this->is_new_record) {
//                                    if (static::model()->findByConditions([$attribute => $attribute_value])) {
//                                        $is_valid = false;
//                                        $this->errors[] = "The {$attribute} is not unique.";
//                                    }
//                                } else {
//                                    if (static::model()->findByConditions([$attribute => $attribute_value, $this->getPrimaryName() => ['!=' => $this->getPrimaryKey()]])) {
//                                        $is_valid = false;
//                                        $this->errors[] = "The {$attribute} is not unique.";
//                                    }
//                                }
//                                break;
                        }
                    }
                }
            }
        }

        $this->afterValid();

        return $is_valid;
    }

    /**
     * @return array|bool|mixed
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
                    $res = Dao::component()->create(static::TABLE_NAME, $values);
                    if ($res) {
                        $this->{$this->_primary_key} = $res->__toString();
                        $this->is_new_record = false;
                    }
                } else {
                    $values = $this->_attributes;
                    $primary_key = 0;
                    if (array_key_exists($this->_primary_key, $this->_attributes)) {
                        $primary_key = $this->_attributes[$this->_primary_key];
                        unset($values[$this->_primary_key]);
                    }
                    $res = Dao::component()->update(static::TABLE_NAME, [$this->_primary_key => new \MongoDB\BSON\ObjectID($primary_key)], ['$set' => $values]);
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
