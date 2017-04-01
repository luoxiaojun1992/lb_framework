<?php

namespace lb\controllers\console;

use lb\components\consts\CodeTpl;
use lb\components\consts\ErrorMsg;
use lb\components\error_handlers\ParamException;
use lb\Lb;

class ModelController extends ConsoleController implements ErrorMsg
{
    /**
     * Create Model
     */
    public function create()
    {
        $argc = $_SERVER['argc'];
        $argv = $_SERVER['argv'];
        if ($argc > 1) {
            $this->generateModel($this->getModelClassName($argv[1]));
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
     * Generate model file
     *
     * @param $modelClassName
     */
    protected function generateModel($modelClassName)
    {
        file_put_contents(Lb::app()->getRootDir() . DIRECTORY_SEPARATOR . '/models/' . $modelClassName . '.php',
            CodeTpl::modelTpl);
    }
}
