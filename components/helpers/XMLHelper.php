<?php
/**
 * Created by PhpStorm.
 * User: 224
 * Date: 2015/11/20
 * Time: 13:49
 * Lb framework xml helper component file
 */

namespace lb\components\helpers;

use lb\BaseClass;

class XMLHelper extends BaseClass
{
    const XML_TPL = '<?xml version="1.0" encoding="UTF-8" ?>';

    public static function encode($data)
    {
        $xml = '';
        if (is_array($data)) {
            $xml = static::arrToXMLString($data);
        } else {
            if (!is_object($data) && !is_resource($data)) {
                $xml = static::arrToXMLString([$data]);
            }
        }
        return $xml;
    }

    protected static function arrToXMLString($array)
    {
        return static::XML_TPL . static::arrToXMLContent($array);
    }

    protected static function arrToXMLContent($array)
    {
        $xml_content = '';
        foreach ($array as $key => $val) {
            if (!is_array($val)) {
                $xml_content .= ("<{$key}>{$val}</{$key}>");
            } else {
                $xml_content .= ("<{$key}>" . static::arrToXMLContent($val) . "</{$key}>");
            }
        }
        return $xml_content;
    }
}
