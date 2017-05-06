<?php

namespace lb\components\swoole;

use lb\BaseClass;

class Mqtt extends BaseClass
{
    public static function decodeValue($data)
    {
        return 256 * ord($data[0]) + ord($data[1]);
    }

    public static function decodeString($data)
    {
        $length = self::decodeValue($data);
        return substr($data, 2, $length);
    }

    public static function mqtt_get_header($data)
    {
        $byte = ord($data[0]);
        $header['type'] = ($byte & 0xF0) >> 4;
        $header['dup'] = ($byte & 0x08) >> 3;
        $header['qos'] = ($byte & 0x06) >> 1;
        $header['retain'] = $byte & 0x01;
        return $header;
    }

    public static function event_connect($data)
    {
        $connect_info['protocol_name'] = self::decodeString($data);
        $offset = strlen($connect_info['protocol_name']) + 2;
        $connect_info['version'] = ord(substr($data, $offset, 1));
        $offset += 1;
        $byte = ord($data[$offset]);
        $connect_info['willRetain'] = ($byte & 0x20 == 0x20);
        $connect_info['willQos'] = ($byte & 0x18 >> 3);
        $connect_info['willFlag'] = ($byte & 0x04 == 0x04);
        $connect_info['cleanStart'] = ($byte & 0x02 == 0x02);
        $offset += 1;
        $connect_info['keepalive'] = self::decodeValue(substr($data, $offset, 2));
        $offset += 2;
        $connect_info['clientId'] = self::decodeString(substr($data, $offset));
        return $connect_info;
    }
}
