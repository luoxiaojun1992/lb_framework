<?php

namespace lb\controllers\console;

use lb\components\consts\CodeTpl;
use lb\components\consts\ErrorMsg;
use lb\components\db\mysql\Connection;
use lb\components\error_handlers\ParamException;
use lb\Lb;

class ModelController extends ConsoleController implements ErrorMsg
{
    const CLASS_NAME_TAG = '{{%className}}';
    const TABLE_NAME_TAG = '{{%tableName}}';
    const ATTRIBUTES_TAG = '{{%attributes}}';
    const LABELS_TAG = '{{%labels}}';

    /**
     * Create Model
     */
    public function create()
    {
        $argc = $_SERVER['argc'];
        $argv = $_SERVER['argv'];
        if ($argc > 2) {
            $modelName = $argv[2];
            $modelClassName = $this->getModelClassName($modelName);
            $this->generateModel($modelClassName, $this->getModelTpl($modelClassName, $this->getTableName($modelName)));
            $this->writeln('Model ' . $modelClassName . ' generated.');
        } else {
            throw new ParamException(ErrorMsg::INVALID_PARAM);
        }
    }

    /**
     * Get model class name
     *
     * @param $modelName
     * @return string
     */
    protected function getModelClassName($modelName)
    {
        return ucfirst($modelName);
    }

    /**
     * Get table name
     *
     * @param $modelName
     * @return string
     */
    protected function getTableName($modelName)
    {
        return strtolower($modelName);
    }

    /**
     * Get model template
     *
     * @param $modelClassName
     * @param $tableName
     * @return string
     */
    protected function getModelTpl($modelClassName, $tableName)
    {
        $modelTpl = str_replace(self::CLASS_NAME_TAG, $modelClassName, CodeTpl::MODEL_TPL);

        $statement = Connection::component()->read_conn->prepare('desc ' . $tableName);
        if ($result = $statement->execute()) {
            $fields = $statement->fetchAll();

            //Assemble Attributes
            $primayKey = '';
            $attributes = '';
            foreach ($fields as $field) {
                $attrName = $field['Field'];
                $defaultValue = $this->formatValue($field['Default'], $field['Type']);
                if ($field['Key'] == 'PRI') {
                    $primayKey = <<<EOF
'{$attrName}' => {$defaultValue}
EOF;
                    $primayKey .= PHP_EOL;
                } else {
                    $attributes .= <<<EOF
'{$attrName}' => {$defaultValue},
EOF;
                    $attributes .= PHP_EOL;
                }
            }
            $modelTpl = str_replace(self::ATTRIBUTES_TAG, rtrim($primayKey . $attributes, PHP_EOL), $modelTpl);

            //Assemble Labels
        }

        return str_replace(self::TABLE_NAME_TAG, $tableName, $modelTpl);
    }

    /**
     * Format Value
     *
     * @param $value
     * @param $type
     * @return float|int|string
     */
    protected function formatValue($value, $type)
    {
        if (strtolower($type) == 'timestamp') {
            $value = '';
        } else if (strtolower($type) == 'datetime') {
            $value = '';
        } else if (strtolower($type) == 'date') {
            $value = '';
        } else if (strtolower($type) == 'time') {
            $value = '';
        } else if (stripos($type, 'int') !== false) {
            $value = intval($value);
        } else if (stripos($type, 'float') !== false) {
            $value = floatval($value);
        } else if (stripos($type, 'decimal') !== false) {
            $value = doubleval($value);
        } else {
            $value = (string)$value;
        }

        return $value;
    }

    /**
     * Generate model file
     *
     * @param $modelClassName
     * @param $modelTpl
     */
    protected function generateModel($modelClassName, $modelTpl)
    {
        file_put_contents(Lb::app()->getRootDir() . DIRECTORY_SEPARATOR . '/models/' . $modelClassName . '.php',
            $modelTpl);
    }
}
