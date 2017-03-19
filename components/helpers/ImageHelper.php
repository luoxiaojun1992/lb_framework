<?php

namespace lb\components\helpers;

use Gregwar\Captcha\CaptchaBuilder;
use lb\BaseClass;
use lb\Lb;

class ImageHelper extends BaseClass
{
    /**
     * @param int $latency millisecond default
     * @param $latencyType 1 millisecond, 2 microsecond
     */
    public static function captcha($latency = 0, $latencyType = 1)
    {
        $builder = new CaptchaBuilder;
        $builder->build();

        Lb::app()->setSession('verify_code', $builder->getPhrase());

        header('Content-type: image/jpeg');

        //Frequency Limit
        if ($latency) {
            $latencyTimes = 1000;
            switch ($latencyType) {
                case 2:
                    $latencyTimes = 1;
            }
            usleep($latency * $latencyTimes);
        }

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
