<?php

namespace lb\components\db\mysql;

use lb\BaseClass;
use lb\components\db\AbstractActiveRecord;
use lb\components\traits\Singleton;

class QueryBuilder extends BaseClass
{
    use Singleton;

    /**
     * @var  AbstractActiveRecord 
     */
    protected $_model;

    /**
     * @var  Dao 
     */
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
     * @param  $model
     * @return bool|QueryBuilder
     */
    public static function find($model)
    {
        if (property_exists(get_called_class(), 'instance')) {
            if (static::$instance instanceof static) {
                /**
                 * @var QueryBuilder $instance
                 */
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
     *
     * @param AbstractActiveRecord $model
     */
    public function __construct(AbstractActiveRecord $model)
    {
        $this->setModel($model);
        $this->setDao(Dao::component());
    }

    /**
     * @param $isRelatedModelExists
     * @param $relatedModelClass
     * @param $selfField
     * @return bool|Dao|null
     */
    protected function getDaoByConditions(&$isRelatedModelExists = false, &$relatedModelClass = null, &$selfField = null)
    {
        if ($this->is_single) {
            $dao = Dao::component()->select(['*'])->from($this->_model->getTableName());
            if (is_array($this->getConditions()) && $this->getConditions()) {
                $dao->where($this->getConditions());
            }
            if (is_array($this->getGroupFields()) && $this->getGroupFields()) {
                $dao->group($this->getGroupFields());
            }
            if (is_array($this->getOrders()) && $this->getOrders()) {
                $dao->order($this->getOrders());
            }
            if ($this->getLimit()) {
                $dao->limit($this->getLimit());
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
     * @param integer $expire
     * @return array|ActiveRecord[]|ActiveRecord
     */
    public function findByConditions($expire = null)
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
                    $related_model_class,
                    $self_field
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
                        /**
                         * @var ActiveRecord $related_model
                         */
                        $related_model = new $related_model_class();
                        $related_model->setAttributes($attributes);
                        $related_model->is_new_record = false;
                        $attributes[$self_field] = $related_model;
                    }
                    /**
                     * @var ActiveRecord $model
                     */
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
     * @param \Closure $callback
     * @param int      $limit
     */
    public function chunkByConditions(
        \Closure $callback,
        $limit = 10000
    ) {
    
        if ($this->is_single) {
            $dao = $this->getDaoByConditions(
                $is_related_model_exists,
                $related_model_class,
                $self_field
            );
            $offset = 0;
            while($result = $dao->limit($offset . ',' . $limit)->findAll()) {
                $offset += $limit;

                $models = [];
                foreach ($result as $attributes) {
                    $model_class = get_class($this->_model);
                    if ($is_related_model_exists && isset($related_model_class) && isset($self_field)) {
                        /**
                         * @var ActiveRecord $related_model
                         */
                        $related_model = new $related_model_class();
                        $related_model->setAttributes($attributes);
                        $related_model->is_new_record = false;
                        $attributes[$self_field] = $related_model;
                    }
                    /**
                     * @var ActiveRecord $model
                     */
                    $model = new $model_class();
                    $model->setAttributes($attributes);
                    $model->is_new_record = false;
                    $models[] = $model;
                }

                call_user_func_array($callback, ['result' => $models]);
            }
        }
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
        $this->setGroupFields(array_merge($this->getGroupFields(), $groupFields));
    }

    /**
     * @param array $orders
     */
    public function order(Array $orders)
    {
        $this->setOrders(array_merge($this->getOrders(), $orders));
    }

    /**
     * @param string $limit
     */
    public function limit($limit = '')
    {
        $this->setLimit($limit);
    }

    /**
     * @param null $cacheExpire
     * @return array|ActiveRecord|ActiveRecord[]
     */
    public function all($cacheExpire = null)
    {
        return $this->findByConditions($cacheExpire);
    }

    /**
     * @param \Closure $callback
     * @param int      $limit
     */
    public function chunk(\Closure $callback, $limit = 10000)
    {
        return $this->chunkByConditions($callback, $limit);
    }

    /**
     * @param null $cacheExpire
     * @return array|ActiveRecord|ActiveRecord[]
     */
    public function one($cacheExpire = null)
    {
        $this->setLimit('1');
        return $this->findByConditions($cacheExpire);
    }

    /**
     * @return int
     */
    public function countByConditions()
    {
        if ($this->is_single) {
            return $this->getDaoByConditions()->count();
        }
        return 0;
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->countByConditions();
    }
}
