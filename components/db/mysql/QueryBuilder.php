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

    /** @var  Dao */
    protected $_dao;

    protected $_conditions;

    protected $_groupFields;

    protected $_orders;

    protected $_limit;

    /**
     * @return mixed
     */
    public function getGroupFields()
    {
        return $this->_groupFields;
    }

    /**
     * @param mixed $groupFields
     */
    public function setGroupFields($groupFields)
    {
        $this->_groupFields = $groupFields;
    }

    /**
     * @return mixed
     */
    public function getOrders()
    {
        return $this->_orders;
    }

    /**
     * @param mixed $orders
     */
    public function setOrders($orders)
    {
        $this->_orders = $orders;
    }

    /**
     * @return mixed
     */
    public function getLimit()
    {
        return $this->_limit;
    }

    /**
     * @param mixed $limit
     */
    public function setLimit($limit)
    {
        $this->_limit = $limit;
    }

    /**
     * @return mixed
     */
    public function getConditions()
    {
        return $this->_conditions;
    }

    /**
     * @param mixed $conditions
     */
    public function setConditions($conditions)
    {
        $this->_conditions = $conditions;
    }

    /**
     * @return Dao
     */
    public function getDao(): Dao
    {
        return $this->_dao;
    }

    /**
     * @param Dao $dao
     */
    public function setDao(Dao $dao)
    {
        $this->_dao = $dao;
    }

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
                /** @var QueryBuilder $instance */
                $instance = static::$instance;
                $instance->setDao(Dao::component());
            } else {
                return (static::$instance = new static($model));
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
        $this->setDao(Dao::component());
    }

    /**
     * @param $isRelatedModelExists
     * @param array $conditions
     * @param $relatedModelClass
     * @param $selfField
     * @param array $group_fields
     * @param array $orders
     * @param string $limit
     * @return bool|Dao|null
     */
    protected function getDaoByConditions(&$isRelatedModelExists, &$relatedModelClass, &$selfField, $conditions = [], $group_fields = [], $orders = [], $limit = '')
    {
        if ($this->is_single) {
            $dao = Dao::component()->select(['*'])->from($this->_model->getTableName());
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
     * @param array $conditions
     * @param array $group_fields
     * @param array $orders
     * @param string $limit
     * @param integer $expire
     * @return array|ActiveRecord[]|ActiveRecord
     */
    public function findByConditions($conditions = [], $group_fields = [], $orders = [], $limit = '', $expire = null)
    {
        if ($this->is_single) {
            $is_related_model_exists = false;
            $result = [];
            if (method_exists($this->_model, 'getCache')) {
                $result = $this->_model->getCache(func_get_args());
            }
            if (!$result) {
                $result = $this->getDaoByConditions(
                    $is_related_model_exists,
                    $conditions,
                    $group_fields,
                    $orders,
                    $limit
                )->findAll();
            }
            if ($result) {
                if (method_exists($this->_model, 'setCache')) {
                    $this->_model->setCache(func_get_args(), $result, $expire);
                }

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

    /**
     * @param array $conditions
     * @return $this
     */
    public function where(Array $conditions = [])
    {
        $this->setConditions(array_merge($this->getConditions(), $conditions));
        return $this;
    }

    /**
     * @param array $groupFields
     */
    public function group(Array $groupFields = [])
    {
        $this->setGroupFields($groupFields);
    }

    /**
     * @param array $orders
     */
    public function order(Array $orders)
    {
        $this->setOrders($orders);
    }



    public function all()
    {
        return $this->findByConditions($this->_conditions);
    }

    public function chunk()
    {

    }

    public function one()
    {

    }
}
