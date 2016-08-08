<?php
/**
 * Created by PhpStorm.
 * User: 224
 * Date: 2016/8/5
 * Time: 14:26
 * Lb framework Listener Interface component file
 */

namespace lb\components\listeners;

use lb\components\events\Base;

interface ListenerInterface
{
    public function handler(Base $event);
}
