<?php
/**
 * Created by PhpStorm.
 * User: 224
 * Date: 2015/12/14
 * Time: 14:42
 * Lb framework validation helper component file
 */

namespace lb\components\helpers;

use UCSDMath\Validation\Validation;

class ValidationHelper
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
        $validator = static::getValidator();
        return $validator->isRequired($value);
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
}
