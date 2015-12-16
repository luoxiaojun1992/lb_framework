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

    public static function download($file_path, $file_name)
    {
        if (file_exists($file_path)) {
            $file_size = filesize($file_path);
            $fp = fopen($file_path, 'r');
            Header('Content-type: application/octet-stream');
            Header('Accept-Ranges: bytes');
            Header('Accept-Length: ' . $file_size);
            Header('Content-Disposition: attachment; filename=' . $file_name);
            echo fread($fp, $file_size);
            fclose($fp);
        }
    }
}
