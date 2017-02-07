<?php

namespace lb\components\helpers;

use Gregwar\Captcha\CaptchaBuilder;
use lb\BaseClass;
use lb\Lb;

class ImageHelper extends BaseClass
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

    public static function getInfo($file_path)
    {
        $info = getimagesize($file_path);
        if ($info) {
            list($width, $height, $type) = $info;
            return [
                'width' => $width,
                'height' => $height,
                'type' => image_type_to_extension($type),
            ];
        }
        return [];
    }

    public static function getWidth($file_path)
    {
        $info = static::getInfo($file_path);
        return $info ? $info['width'] : false;
    }

    public static function getHeight($file_path)
    {
        $info = static::getInfo($file_path);
        return $info ? $info['height'] : false;
    }

    public static function getType($file_path)
    {
        $info = static::getInfo($file_path);
        return $info ? $info['type'] : false;
    }
}
