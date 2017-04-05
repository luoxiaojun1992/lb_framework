<?php

namespace lb\models;

use lb\components\db\mysql\ActiveRecord;

/**
 * Class SwooleSession
 * @property integer $id
 * @property integer $expire
 * @property string $data
 * @inheritdoc
 * @package lb\models
 */
class SwooleSession extends ActiveRecord
{
    // * Required Properties
    const TABLE_NAME = 'swoole_session';
    protected $_primary_key = 'id';
    protected $_attributes = [
        'id' => 0,
        'expire' => 0,
        'data' => '',
    ];
    public $labels = [
        'id' => 'ID',
        'expire' => 'Expire',
        'data' => 'Data',
    ];

    protected $rules = [
        [['data'], 'required'],
    ];

    public function beforeSave()
    {
        return parent::beforeSave();
    }
}
