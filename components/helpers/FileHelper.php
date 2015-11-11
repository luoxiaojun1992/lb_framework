<?php
/**
 * Created by PhpStorm.
 * User: 224
 * Date: 2015/11/11
 * Time: 15:05
 * Lb framework file helper component file
 */

namespace lb\components\helpers;

class FileHelper
{
    public static function getExtensionName($file_path)
    {
        $file_extension_name = '';
        if (file_exists($file_path)) {
            $file_extension_name = pathinfo($file_path)['extension'];
        }
        return $file_extension_name;
    }
}
