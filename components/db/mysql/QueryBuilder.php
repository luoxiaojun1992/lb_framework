<?php

namespace lb\components\db\mysql;

use lb\BaseClass;
use lb\components\traits\Singleton;

class QueryBuilder extends BaseClass
{
    use Singleton;

    /**
     * Create Query Builder
     *
     * @return bool|QueryBuilder
     */
    public static function find()
    {
        if (property_exists(get_called_class(), 'instance')) {
            if (static::$instance instanceof static) {
                return static::$instance;
            } else {
                $newQueryBuilder = new static();
                static::$instance = $newQueryBuilder;
                return static::$instance;
            }
        }
        return false;
    }
}
