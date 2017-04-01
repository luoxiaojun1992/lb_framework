<?php

namespace lb\components\consts;

interface CodeTpl
{
    const CLASS_NAME_TAG = '{{%className}}';
    const TABLE_NAME_TAG = '{{%tableName}}';
    const ATTRIBUTES_TAG = '{{%attributes}}';
    const LABELS_TAG = '{{%labels}}';
    const PRIMARY_KEY_TAG = '{{%primaryKey}}';
    const PROPERTY_COMMENTS_TAG = '{{%propertyComments}}';

    const MODEL_TPL = <<<'EOF'
<?php

namespace app\models;

use Carbon\Carbon;
use lb\components\db\mysql\ActiveRecord;

/**
 * Class {{%className}}
{{%propertyComments}}
 * @inheritdoc
 * @package app\components
 */
class {{%className}} extends ActiveRecord
{
    // * Required Properties
    const TABLE_NAME = '{{%tableName}}';
    protected $_primary_key = '{{%primaryKey}}';
    protected $_attributes = [
    {{%attributes}}
    ];
    public $labels = [
    {{%labels}}
    ];
    protected static $_instance = false;

    protected $rules = [];

    /**
     * @return bool
     */
    public function beforeCreate()
    {
//        $this->created_at = $this->updated_at = Carbon::now()->toDateTimeString();

        return parent::beforeCreate();
    }

    /**
     * @return bool
     */
    public function beforeUpdate()
    {
//        $this->updated_at = Carbon::now()->toDateTimeString();

        return parent::beforeUpdate();
    }
}

EOF;

}
