<?php

namespace lb\components\db\mysql;

use lb\BaseClass;
use lb\components\db\AbstractActiveRecord;
use lb\components\traits\Singleton;

class QueryBuilder extends BaseClass
{
    use Singleton;

    /** @var  AbstractActiveRecord */
    protected $_model;

    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->_model;
    }

    /**
     * @param mixed $model
     */
    public function setModel($model)
    {
        $this->_model = $model;
    }

    /**
     * Create Query Builder
     *
     * @param $model
     * @return bool|QueryBuilder
     */
    public static function find($model)
    {
        if (property_exists(get_called_class(), 'instance')) {
            if (static::$instance instanceof static) {
                return static::$instance;
            } else {
                $newQueryBuilder = new static($model);
                static::$instance = $newQueryBuilder;
                return static::$instance;
            }
        }
        return false;
    }

    /**
     * QueryBuilder constructor.
     * @param AbstractActiveRecord $model
     */
    public function __construct(AbstractActiveRecord $model)
    {
        $this->setModel($model);
    }

    /**
     * @param $isRelatedModelExists
     * @param $relatedModelClass
     * @param $selfField
     * @return bool|Dao|null
     */
    protected function getDaoByAll(&$isRelatedModelExists, &$relatedModelClass, &$selfField)
    {
        if ($this->is_single) {
            $dao = Dao::component()->select(['*'])
                ->from($this->_model->getTableName());

            $isRelatedModelExists = false;
            if ($this->_model->getRelations() && count($this->_model->getRelations()) >= 3) {
                list($selfField, $joined_table, $joined_table_field) = $this->_model->getRelations();
                $relatedModelClass = 'app\models\\' . ucfirst($joined_table);
                if (array_key_exists($selfField, $this->_model->getAttributes()) && class_exists($relatedModelClass)) {
                    $isRelatedModelExists = true;
                    $related_model_fields = (new $relatedModelClass())->getFields();
                    foreach ($related_model_fields as $key => $related_model_field) {
                        $related_model_fields[$key] = implode('.', [$joined_table, $related_model_field]) . ' AS ' . implode('_', [$joined_table, $related_model_field]);
                    }
                    $self_fields = $this->_model->getFields();
                    foreach ($self_fields as $key => $field) {
                        $self_fields[$key] = implode('.', [$this->_model->getTableName(), $field]);
                    }
                    $fields = array_merge($related_model_fields, $self_fields);
                    $dao->select($fields)
                        ->join($joined_table, [$selfField => implode('.', [$joined_table, $joined_table_field])]);
                }
            }

            return $dao;
        }

        return null;
    }

    /**
     * @return array|ActiveRecord|ActiveRecord[]
     */
    public function all()
    {
        if ($this->is_single) {
            $result = $this->getDaoByAll($is_related_model_exists, $related_model_class, $self_field)->findAll();
            if ($result) {
                $models = [];
                foreach ($result as $attributes) {
                    $model_class = get_class($this->_model);
                    if ($is_related_model_exists && isset($related_model_class) && isset($self_field)) {
                        /** @var ActiveRecord $related_model */
                        $related_model = new $related_model_class();
                        $related_model->setAttributes($attributes);
                        $related_model->is_new_record = false;
                        $attributes[$self_field] = $related_model;
                    }
                    /** @var ActiveRecord $model */
                    $model = new $model_class();
                    $model->setAttributes($attributes);
                    $model->is_new_record = false;
                    $models[] = $model;
                }
                return !$models || count($models) > 1 ? $models : $models[0];
            }
        }
        return [];
    }
}
