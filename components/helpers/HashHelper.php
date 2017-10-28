<?php

namespace lb\components\helpers;

use lb\BaseClass;

class HashHelper extends BaseClass
{
    const MD5_HASH = 'md5';
    const SHA1_HASH = 'sha1';

    /**
     * @param $str
     * @param string   $algo
     * @param string   $hmacKey
     * @param $rawOuput
     * @return string
     */
    public static function hash($str, $algo = 'md5', $hmacKey = '', $rawOuput = false)
    {
        if ($hmacKey) {
            return hash_hmac($algo, $str, $hmacKey, $rawOuput);
        }

        switch ($algo) {
        case 'md5':
            $hashCode = md5($str, $rawOuput);
            break;
        case 'sha1':
            $hashCode = sha1($str, $rawOuput);
            break;
        default:
            $hashCode = md5($str, $rawOuput);
        }

        return $hashCode;
    }

    /**
     * @param $str
     * @return int
     */
    public static function flexiHash($str)
    {
        $md5 = substr(self::hash($str), 0, 8);
        $seed = 31;
        $hash = 0;
        for ($i = 0; $i < 8; $i++) {
            $hash = $hash * $seed + ord($md5{$i});
            $i++;
        }
        return $hash & 0x7FFFFFFF;
    }
}
