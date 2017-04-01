<?php

namespace lb\components\consts;

interface CodeTpl
{
    const MODEL_TPL = <<<'EOF'
<?php

namespace app\models;

use Carbon\Carbon;
use lb\components\db\mysql\ActiveRecord;

/**
 * Class {{%className}}
 * @property integer $id
 * @property string $created_at
 * @property string $updated_at
 * @inheritdoc
 * @package app\components
 */
class {{%className}} extends ActiveRecord
{
    // * Required Properties
    const TABLE_NAME = '{{%tableName}}';
    protected $_primary_key = 'id';
    protected $_attributes = [
        'id' => 0,
        'created_at' => '',
        'updated_at' => '',
    ];
    public $labels = [
        'id' => 'ID',
        'created_at' => 'Created At',
        'updated_at' => 'Updated At',
    ];
    protected static $_instance = false;

    protected $rules = [];

    /**
     * @return bool
     */
    public function beforeCreate()
    {
        $this->created_at = $this->updated_at = Carbon::now()->toDateTimeString();

        return parent::beforeCreate();
    }

    /**
     * @return bool
     */
    public function beforeUpdate()
    {
        $this->updated_at = Carbon::now()->toDateTimeString();

        return parent::beforeUpdate();
    }
}
EOF;

}
