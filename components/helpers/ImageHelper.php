<?php
/**
 * Created by PhpStorm.
 * User: luoxiaojun
 * Date: 15/12/16
 * Time: 下午11:01
 * Lb framework image helper component file
 */

namespace lb\components\helpers;

use Gregwar\Captcha\CaptchaBuilder;
use lb\Lb;

class ImageHelper
{
    public static function captcha()
    {
        $builder = new CaptchaBuilder;
        $builder->build();

        $phrase = $builder->getPhrase();
        Lb::app()->setSession('verify_code', $phrase);

        header('Content-type: image/jpeg');
        $builder->output();
    }
}
