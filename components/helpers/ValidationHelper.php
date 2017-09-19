<?php

namespace lb\components\helpers;

use lb\BaseClass;
use UCSDMath\Validation\Validation;

class ValidationHelper extends BaseClass
{
    protected static $validator = false;

    public function __clone()
    {
        // TODO: Implement __clone() method.
    }

    protected static function getValidator()
    {
        if (static::$validator && static::$validator instanceof Validation) {
            return static::$validator;
        } else {
            return (static::$validator = new Validation());
        }
    }

    public static function isUrl($url)
    {
        $validator = static::getValidator();
        return $validator->isValidURL($url);
    }

    public static function isRequired($value)
    {
        return (bool)$value;
    }

    public static function isEmail($email_address)
    {
        $validator = static::getValidator();
        return $validator->isValidEmail($email_address);
    }

    public static function isIP($ip_address)
    {
        $validator = static::getValidator();
        return $validator->isValidIP($ip_address);
    }

    /**
     * 验证手机号合法性
     *
     * @param $mobile
     * @return bool
     */
    public static function isMobile($mobile)
    {
        $reg = '/^((\(\d{3}\))|(\d{3}\-))?(1[345789]\d{9})$/';
        $res = preg_match($reg, $mobile);
        return $res == 1 ? true : false;
    }
}
