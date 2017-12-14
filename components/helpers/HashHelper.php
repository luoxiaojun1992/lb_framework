<?php

namespace lb\components\helpers;

use lb\BaseClass;
use lastguest\Murmur;
use lb\components\error_handlers\ParamException;

class HashHelper extends BaseClass
{
    //Hash Algorithms
    const MD5_HASH = 'md5';
    const SHA1_HASH = 'sha1';
    const CRYPT_HASH = 'crypt';
    const PASSWORD_HASH = 'password_hash';

    /**
     * Hashing
     *
     * @param  $str
     * @param  string $algo
     * @param  string $hmacKey
     * @param  bool   $rawOuput
     * @param  null   $salt
     * @param  int    $passwordAlgo
     * @param  array  $passwordOptions
     * @return bool|string
     */
    public static function hash($str, $algo = self::MD5_HASH, $hmacKey = '', $rawOuput = false, $salt = null, $passwordAlgo = PASSWORD_DEFAULT, $passwordOptions = [])
    {
        if ($hmacKey && in_array($algo, [self::MD5_HASH, self::SHA1_HASH])) {
            return hash_hmac($algo, $str, $hmacKey, $rawOuput);
        }

        switch ($algo) {
        case self::MD5_HASH:
            $hashCode = md5($str, $rawOuput);
            break;
        case self::SHA1_HASH:
            $hashCode = sha1($str, $rawOuput);
            break;
        case self::CRYPT_HASH:
            $hashCode = crypt($str, $salt);
            break;
        case self::PASSWORD_HASH:
            $hashCode = password_hash($str, $passwordAlgo, $passwordOptions);
            break;
        default:
            $hashCode = md5($str, $rawOuput);
        }

        return $hashCode;
    }

    /**
     * Flexi hashing
     *
     * @param  $str
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

    /**
     * Murmurhash 3
     *
     * @param  $str
     * @param  int  $seed
     * @param  bool $isInt
     * @param  int  $version
     * @return mixed
     * @throws ParamException
     */
    public static function murmurhash($str, int $seed = 0, $isInt = false, $version = 3)
    {
        if ($version != 3) {
            throw new ParamException('Murmurhash' . $version . ' not supported');
        }

        return $isInt ? Murmur::hash3_int($str, $seed) : Murmur::hash3($str, $seed);
    }
}
