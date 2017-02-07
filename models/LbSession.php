<?php
/**
 * Created by PhpStorm.
 * User: luoxiaojun
 * Date: 16/3/12
 * Time: 上午11:15
 */

namespace lb\models;

use lb\components\db\mysql\ActiveRecord;

/**
 * Class LbSession
 * @property integer $id
 * @property integer $expire
 * @property string $data
 * @inheritdoc
 * @package lb\models
 */
class LbSession extends ActiveRecord
{
    // * Required Properties
    const TABLE_NAME = 'lb_session';
    protected $_primary_key = 'id';
    protected $_attributes = [
        'id' => '',
        'expire' => 0,
        'data' => '',
    ];
    public $labels = [
        'id' => 'ID',
        'expire' => 'Expire',
        'data' => 'Data',
    ];
    protected static $_instance = false;

    protected $rules = [
        [['data'], 'required'],
    ];

    public function beforeSave()
    {
        return parent::beforeSave();
    }
}
