<?php

namespace lb\components\consts;

interface CodeTpl
{
    const modelTpl = <<<'EOF'
<?php

namespace app\models;

use Carbon\Carbon;
use lb\components\db\mysql\ActiveRecord;

/**
 * Class Eagle
 * @property string $path
 * @property string $url
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 * @inheritdoc
 * @package app\components
 */
class Eagle extends ActiveRecord
{
    // * Required Properties
    const TABLE_NAME = 'eagle';
    protected $_primary_key = 'id';
    protected $_attributes = [
        'id' => 0,
        'eagle_id' => '',
        'service_endpoint' => '',
        'request_duration' => 0.0,
        'status' => 0,
        'created_at' => '',
        'updated_at' => '',
    ];
    public $labels = [
        'id' => 'ID',
        'eagle_id' => 'Eagle ID',
        'service_endpoint' => 'Service Endpoint',
        'request_duration' => 'Request Duration',
        'status' => 'Status',
        'created_at' => 'Created At',
        'updated_at' => 'Updated At',
    ];
    protected static $_instance = false;

    protected $rules = [
        [['eagle_id', 'service_endpoint', 'request_duration'], 'required'],
    ];

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
