<?php
/**
 * Created by PhpStorm.
 * User: 224
 * Date: 2015/11/11
 * Time: 10:35
 * Lb framework mysql db dao file
 */

namespace lb\components\db\mongodb;

use lb\BaseClass;

class Dao extends BaseClass
{
    protected static $instance = false;

    /**
     * @return bool|static
     */
    public static function component()
    {
        if (static::$instance instanceof static) {
            $instance = static::$instance;
            return $instance;
        } else {
            return (static::$instance = new static());
        }
    }

    public function __clone()
    {
        // TODO: Implement __clone() method.
    }

    public function create($collection, $document)
    {
        // Create a bulk write object and add our insert operation
        $bulk = new \MongoDB\Driver\BulkWrite;
        $bulk->insert($document);

        // Construct a write concern
        $wc = new \MongoDB\Driver\WriteConcern(
            // Guarantee that writes are acknowledged by a majority of our nodes
            \MongoDB\Driver\WriteConcern::MAJORITY,
            // But only wait 1000ms because we have an application to run!
            1000,
            true
        );

        try {
            /* Specify the full namespace as the first argument, followed by the bulk
             * write object and an optional write concern. MongoDB\Driver\WriteResult is
             * returned on success; otherwise, an exception is thrown. */
            $result = Connection::component()->_conn->executeBulkWrite("db.{$collection}", $bulk, $wc);
            var_dump($result);
        } catch (\MongoDB\Driver\Exception\Exception $e) {
            $result = Connection::component(Connection::component()->containers, true)->_conn->executeBulkWrite("db.collection", $bulk, $wc);
            var_dump($result);
        }
    }
}
