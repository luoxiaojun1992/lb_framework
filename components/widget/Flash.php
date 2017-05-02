<?php

namespace lb\components\widget;

use lb\controllers\BaseController;

class Flash extends Base
{
    public static function render($flashKey, BaseController $controller)
    {
        return sprintf('<p>%s</p>', $controller->getFlash($flashKey));
    }
}
