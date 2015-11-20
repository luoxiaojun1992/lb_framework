<?php
/**
 * Created by PhpStorm.
 * User: 224
 * Date: 2015/11/20
 * Time: 13:49
 * Lb framework xml helper component file
 */

namespace lb\components\helpers;

class XMLHelper
{
    public static function encode($data)
    {
        $xml = '';
        if (is_array($data)) {
            $xml = self::arrToXMLString($data);
        } else {
            if (!is_object($data) && !is_resource($data)) {
                $xml = self::arrToXMLString([$data]);
            }
        }
        return $xml;
    }

    public static function arrToXMLString($array)
    {
        return self::arrToXMLElement($array)->asXML();
    }

    public static function arrToXMLElement($array, $xmlElement = false)
    {
        $xmlTpl = <<<XML
<?xml version="1.0" encoding="UTF-8" ?>
<xml></xml>
XML;
        if (!$xmlElement) {
            $xmlElement = simplexml_load_string($xmlTpl, 'SimpleXMLElement', LIBXML_NOCDATA);
        }
        foreach ($array as $key => $val) {
            if (!is_array($val)) {
                $xmlElement->addChild($key, $val);
            } else {
                $xmlElement->addChild($key);
                $xmlElement = self::arrToXML($val, $xmlElement);
            }
        }
        return $xmlElement;
    }
}
