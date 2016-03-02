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
use lb\components\helpers\ArrayHelper;
use lb\Lb;

class Dao extends BaseClass
{
    protected static $instance = false;
    protected $_db_name = 'db';
    protected $_wc = null;
    protected $_rp = null;

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

    public function __construct()
    {
        // Get DB Name
        $mongoDbConfig = Lb::app()->getDbConfig(Connection::DB_TYPE);
        if ($mongoDbConfig) {
            $this->_db_name = $mongoDbConfig['dbname'];
        }

        // Construct a write concern
        $this->_wc = new \MongoDB\Driver\WriteConcern(
        // Guarantee that writes are acknowledged by a majority of our nodes
            \MongoDB\Driver\WriteConcern::MAJORITY,
            // But only wait 1000ms because we have an application to run!
            1000,
            true
        );

        // Construct a read preference
        $this->_rp = new \MongoDB\Driver\ReadPreference(
        /* We prefer to read from a secondary, but are OK with reading from the
         * primary if necessary (e.g. secondaries are offline) */
            \MongoDB\Driver\ReadPreference::RP_SECONDARY_PREFERRED,
            // Specify some tag sets for our preferred nodes
            [
                // Prefer reading from our west coast datacenter in Iceland
//                ["country" => "iceland", "datacenter" => "west"],
//                // Fall back to any datacenter in Iceland
//                ["country" => "iceland"],
                // If Iceland is offline, read from whatever is available
                [],
            ]
        );
    }

    public function __clone()
    {
        // TODO: Implement __clone() method.
    }

    public function create($collection, $document)
    {
        // Create a bulk write object and add our insert operation
        $bulk = new \MongoDB\Driver\BulkWrite;
        if (ArrayHelper::array_depth($document) == 1) {
            $oid = $bulk->insert($document);
        } else {
            $oid = [];
            foreach ($document as $item) {
                $oid[] = $bulk->insert($item);
            }
        }

        try {
            /* Specify the full namespace as the first argument, followed by the bulk
             * write object and an optional write concern. MongoDB\Driver\WriteResult is
             * returned on success; otherwise, an exception is thrown. */
            $result = Connection::component()->_conn->executeBulkWrite(implode('.', [$this->_db_name, $collection]), $bulk, $this->_wc);
            if (is_array($oid)) {
                if ($result->getInsertedCount() == count($oid)) {
                    return $oid;
                }
            } else {
                if ($result->getInsertedCount()) {
                    return $oid;
                }
            }
            return false;
        } catch (\MongoDB\Driver\Exception\Exception $e) {
            $result = Connection::component(Connection::component()->containers, true)->_conn->executeBulkWrite(implode('.', [$this->_db_name, $collection]), $bulk, $this->_wc);
            if (is_array($oid)) {
                if ($result->getInsertedCount() == count($oid)) {
                    return $oid;
                }
            } else {
                if ($result->getInsertedCount()) {
                    return $oid;
                }
            }
            return false;
        }
    }

    public function delete($collection, $filter, $limit = 1)
    {
        /* Specify some command options for the update:
         *
         *  * limit (integer): Deletes all matching documents when 0 (false). Otherwise,
         *    only the first matching document is deleted. */
        $options = ["limit" => $limit];

        // Create a bulk write object and add our delete operation
        $bulk = new \MongoDB\Driver\BulkWrite;
        $bulk->delete($filter, $options);

        try {
            /* Specify the full namespace as the first argument, followed by the bulk
             * write object and an optional write concern. MongoDB\Driver\WriteResult is
             * returned on success; otherwise, an exception is thrown. */
            $result = Connection::component()->_conn->executeBulkWrite(implode('.', [$this->_db_name, $collection]), $bulk, $this->_wc);
            if ($result->getDeletedCount()) {
                return true;
            }
            return false;
        } catch (\MongoDB\Driver\Exception\Exception $e) {
            $result = Connection::component(Connection::component()->containers, true)->_conn->executeBulkWrite(implode('.', [$this->_db_name, $collection]), $bulk, $this->_wc);
            if ($result->getDeletedCount()) {
                return true;
            }
            return false;
        }
    }

    public function update($collection, $filter, $newObj, $multi = true, $upsert = false)
    {
        // Specify the search criteria and update operations (or replacement document)
//        $filter = ["hello" => "world"];
//        $newObj = ['$set' => ["hello" => "wonderful world"]];

        /* Specify some command options for the update:
         *
         *  * multi (boolean): Updates all matching documents when true; otherwise, only
         *    the first matching document is updated. Defaults to false.
         *  * upsert (boolean): If there is no matching document, create a new document
         *    from $filter and $newObj. Defaults to false.
         */
        $options = ["multi" => $multi, "upsert" => $upsert];

        // Create a bulk write object and add our update operation
        $bulk = new \MongoDB\Driver\BulkWrite;
        $bulk->update($filter, $newObj, $options);

        try {
            /* Specify the full namespace as the first argument, followed by the bulk
             * write object and an optional write concern. MongoDB\Driver\WriteResult is
             * returned on success; otherwise, an exception is thrown. */
            $result = Connection::component()->_conn->executeBulkWrite(implode('.', [$this->_db_name, $collection]), $bulk, $this->_wc);
            if ($result->getModifiedCount()) {
                return true;
            }
            return false;
        } catch (\MongoDB\Driver\Exception\Exception $e) {
            $result = Connection::component(Connection::component()->containers, true)->_conn->executeBulkWrite(implode('.', [$this->_db_name, $collection]), $bulk, $this->_wc);
            if ($result->getModifiedCount()) {
                return true;
            }
            return false;
        }
    }

    public function read($collection, $filter)
    {
        /* Construct a query with an empty filter (i.e. "select all") */
        $query = new \MongoDB\Driver\Query($filter);

        try {
            /* Specify the full namespace as the first argument, followed by the query
             * object and an optional read preference. MongoDB\Driver\Cursor is returned
             * success; otherwise, an exception is thrown. */
            $cursor = Connection::component()->_conn->executeQuery(implode('.', [$this->_db_name, $collection]), $query, $this->_rp);
            $result = [];
            // Iterate over all matched documents
            foreach ($cursor as $document) {
                $result[] = $document;
            }
            return $result;
        } catch (\MongoDB\Driver\Exception\Exception $e) {
            $cursor = Connection::component(Connection::component()->containers, true)->_conn->executeQuery(implode('.', [$this->_db_name, $collection]), $query, $this->_rp);
            $result = [];
            // Iterate over all matched documents
            foreach ($cursor as $document) {
                $result[] = $document;
            }
            return $result;
        }
    }
}
