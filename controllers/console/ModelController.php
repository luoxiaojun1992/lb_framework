<?php

namespace lb\controllers\console;

use lb\components\consts\CodeTpl;
use lb\components\consts\ErrorMsg;
use lb\components\db\mysql\Connection;
use lb\components\error_handlers\ParamException;
use lb\Lb;

/**
 * Model Generator
 *
 * Class ModelController
 * @package lb\controllers\console
 */
class ModelController extends ConsoleController implements ErrorMsg
{
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
        $modelTpl = str_replace(CodeTpl::CLASS_NAME_TAG, $modelClassName, CodeTpl::MODEL_TPL);

        /** @var \PDOStatement $statement */
        $statement = Connection::component()->read_conn->prepare('desc ' . $tableName);
        if ($result = $statement->execute()) {
            $fields = $statement->fetchAll();

            //Assemble Attributes & Labels
            $primaryKeyAttr = '';
            $attributes = '';

            $primaryKeyLabel = '';
            $labels = '';

            $primaryKey = '';

            $primaryKeyComment = '';
            $propertyComments = '';

            $setter = '';
            $getter = '';

            foreach ($fields as $field) {
                $attrName = $field['Field'];
                $defaultValue = $this->formatValue($field['Default'], $field['Type']);
                $label = $this->formatLabel($attrName);
                $commentType = $this->getCommentType($field['Type']);
                $camelAttrName = $this->formatAttr($attrName);

                //Setter
                $setter .= <<<EOF
    public function set{$camelAttrName}(\$value)
    {
        \$this->{$attrName} = \$value;
    }
EOF;
                $setter .= (str_repeat(PHP_EOL, 2));

                //Getter
                $getter .= <<<EOF
    public function get{$camelAttrName}()
    {
        return \$this->{$attrName};
    }

EOF;
                $getter .= (str_repeat(PHP_EOL, 2));

                if ($field['Key'] == 'PRI') {
                    //Primary Key
                    $primaryKey = $attrName;

                    //Primary Key Attribute
                    $primaryKeyAttr = <<<EOF
    '{$attrName}' => {$defaultValue},
EOF;
                    $primaryKeyAttr .= PHP_EOL;

                    //Primary Key Label
                    $primaryKeyLabel = <<<EOF
    '{$attrName}' => '{$label}',
EOF;
                    $primaryKeyLabel .= PHP_EOL;

                    //Primary Key Comment
                    $primaryKeyComment = <<<EOF
 * @property {$commentType} \${$attrName}
EOF;
                    $primaryKeyComment .= PHP_EOL;
                } else {
                    //Attributes
                    $attributes .= <<<EOF
        '{$attrName}' => {$defaultValue},
EOF;
                    $attributes .= PHP_EOL;

                    //Labels
                    $labels .= <<<EOF
        '{$attrName}' => '{$label}',
EOF;
                    $labels .= PHP_EOL;

                    //Property Comment
                    $propertyComments .= <<<EOF
 * @property {$commentType} \${$attrName}
EOF;
                    $propertyComments .= PHP_EOL;
                }
            }
            $modelTpl = str_replace(CodeTpl::ATTRIBUTES_TAG, rtrim($primaryKeyAttr . $attributes, PHP_EOL), $modelTpl);
            $modelTpl = str_replace(CodeTpl::LABELS_TAG, rtrim($primaryKeyLabel . $labels, PHP_EOL), $modelTpl);
            $modelTpl = str_replace(CodeTpl::PROPERTY_COMMENTS_TAG,
                rtrim($primaryKeyComment . $propertyComments, PHP_EOL),
                $modelTpl);
            $modelTpl = str_replace(CodeTpl::PRIMARY_KEY_TAG, $primaryKey, $modelTpl);
            $modelTpl = str_replace(CodeTpl::SETTER, $setter, $modelTpl);
            $modelTpl = str_replace(CodeTpl::GETTER, $getter, $modelTpl);
        }

        return str_replace(CodeTpl::TABLE_NAME_TAG, $tableName, $modelTpl);
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
            $value = '\'\'';
        } else if (strtolower($type) == 'datetime') {
            $value = '\'\'';
        } else if (strtolower($type) == 'date') {
            $value = '\'\'';
        } else if (strtolower($type) == 'time') {
            $value = '\'\'';
        } else if (stripos($type, 'int') !== false) {
            $value = intval($value);
        } else if (stripos($type, 'float') !== false) {
            $value = floatval($value);
        } else if (stripos($type, 'decimal') !== false) {
            $value = doubleval($value);
        } else {
            $value = '\'' . (string)$value . '\'';
        }

        return $value;
    }

    /**
     * Format attribute label
     *
     * @param $attrName
     * @return string
     */
    protected function formatLabel($attrName)
    {
        if (strpos($attrName, '_') !== false) {
            $tempArr = explode('_', $attrName);
            foreach ($tempArr as $key => $item) {
                $tempArr[$key] = ucfirst(strtolower($item));
            }
            $attrName = implode(' ', $tempArr);
        } else {
            $attrName = ucfirst($attrName);
            $cloneAttrName = $attrName;
            for ($i = 0; $i < mb_strlen($cloneAttrName, 'UTF8'); ++$i) {
                $asciiCode = ord($cloneAttrName[$i]);
                if ($asciiCode >= 65 && $asciiCode <= 90) {
                    str_replace($cloneAttrName[$i], ' ' . $cloneAttrName[$i], $attrName);
                }
            }
            $attrName = trim($attrName);
        }

        return $attrName;
    }

    /**
     * Format attr name
     *
     * @param $attrName
     * @return string
     */
    protected function formatAttr($attrName)
    {
        if (strpos($attrName, '_') !== false) {
            $tempArr = explode('_', $attrName);
            foreach ($tempArr as $key => $item) {
                $tempArr[$key] = ucfirst(strtolower($item));
            }
            $attrName = implode('', $tempArr);
        } else {
            $attrName = ucfirst($attrName);
        }

        return $attrName;
    }

    /**
     * Get comment property variable type
     *
     * @param $propertyType
     * @return string
     */
    protected function getCommentType($propertyType)
    {
        $commentType = 'string';
        if (stripos($propertyType, 'int') !== false) {
            $commentType = 'integer';
        } else if (stripos($propertyType, 'float') !== false) {
            $commentType = 'float';
        } else if (stripos($propertyType, 'decimal') !== false) {
            $commentType = 'double';
        }

        return $commentType;
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
