<?php

namespace lb\controllers\console;

use lb\components\consts\CodeTpl;
use lb\components\consts\ErrorMsg;
use lb\components\error_handlers\ParamException;
use lb\Lb;

class ModelController extends ConsoleController implements ErrorMsg
{
    const CLASS_NAME_TAG = '{{%className}}';
    const TABLE_NAME_TAG = '{{%tableName}}';

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
        return str_replace(self::TABLE_NAME_TAG, $tableName, $modelTpl);
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
