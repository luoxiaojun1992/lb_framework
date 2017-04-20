<?php

namespace lb\components\helpers;

use lb\BaseClass;

class CryptHelper extends BaseClass
{
    const FORMAT_HEX = 'hex';
    const FORMAT_BASE64 = 'base64';

    const ZERO_IV = 'zero_iv';

    /**
     * RSA Public Encrypt
     *
     * @param $str
     * @param $publicKeyPath
     * @param int $padding
     * @param $format
     * @return string
     */
    public static function rsaPublicEncrypt(
        $str,
        $publicKeyPath,
        $padding = OPENSSL_PKCS1_PADDING,
        $format = self::FORMAT_HEX
    )
    {
        openssl_public_encrypt(
            trim($str),
            $cryrted,
            openssl_get_publickey(file_get_contents($publicKeyPath)),
            $padding
        );

        $cryrted = trim($cryrted);

        switch ($format) {
            case self::FORMAT_HEX:
                return bin2hex($cryrted);
            case self::FORMAT_BASE64:
                return EncodeHelper::base64Encode($cryrted);
        }

        return bin2hex($cryrted);
    }

    /**
     * RSA Private Decrypt
     *
     * @param $str
     * @param $privateKeyPath
     * @param string $passPhrase
     * @param int $padding
     * @param $format
     * @return string
     */
    public static function rsaPrivateDecrypt(
        $str,
        $privateKeyPath,
        $passPhrase = '',
        $padding = OPENSSL_PKCS1_PADDING,
        $format = self::FORMAT_HEX
    )
    {
        $str = trim($str);

        switch ($format) {
            case self::FORMAT_HEX:
                $str = hex2bin($str);
                break;
            case self::FORMAT_BASE64:
                $str = EncodeHelper::base64Decode($str);
                break;
            default:
                $str = hex2bin($str);
        }

        openssl_private_decrypt(
            $str,
            $decrypted,
            openssl_get_privatekey(file_get_contents($privateKeyPath), $passPhrase),
            $padding
        );

        return trim($decrypted);
    }

    /**
     * RSA Sign
     *
     * @param $str
     * @param $privateKeyPath
     * @param int $algo
     * @param string $passPhrase
     * @param string $format
     * @return string
     */
    public static function opensslSign(
        $str,
        $privateKeyPath,
        $algo = OPENSSL_ALGO_SHA1,
        $passPhrase = '',
        $format = self::FORMAT_HEX

    )
    {
        $privateKey = openssl_pkey_get_private(file_get_contents($privateKeyPath), $passPhrase);

        openssl_sign(trim($str), $singature, $privateKey, $algo);

        openssl_free_key($privateKey);

        $singature = trim($singature);

        switch ($format) {
            case self::FORMAT_HEX:
                return bin2hex($singature);
            case self::FORMAT_BASE64:
                return EncodeHelper::base64Encode($singature);
        }

        return bin2hex(trim($singature));
    }

    /**
     * RSA Verify Sign
     *
     * @param $str
     * @param $sinature
     * @param $publicKeyPath
     * @param int $algo
     * @param string $format
     * @return bool
     */
    public static function opensslVerify(
        $str,
        $sinature,
        $publicKeyPath,
        $algo = OPENSSL_ALGO_SHA1,
        $format = self::FORMAT_HEX
    )
    {
        $publicKey = openssl_pkey_get_public(file_get_contents($publicKeyPath));

        $sinature = trim($sinature);

        switch ($format) {
            case self::FORMAT_HEX:
                $sinature = hex2bin($sinature);
                break;
            case self::FORMAT_BASE64:
                $sinature = EncodeHelper::base64Decode($sinature);
                break;
            default:
                $sinature = hex2bin($sinature);
        }

        $res = openssl_verify(trim($str), $sinature, $publicKey, $algo);

        openssl_free_key($publicKey);

        if ($res == 1) {
            return true;
        }

        return false;
    }

    /**
     * AES/CBC/PKCS5Padding Encrypter
     *
     * @param $str
     * @param $key
     * @param string $method
     * @param int $options
     * @param string $iv
     * @param string $format
     * @return string
     */
    public static function opensslEncrypt(
        $str,
        $key,
        $method = 'AES-256-CBC',
        $options = OPENSSL_RAW_DATA,
        $iv = self::ZERO_IV,
        $format = self::FORMAT_HEX
    )
    {
        if ($iv == self::ZERO_IV) {
            $zeroPack = pack('i*', 0);
            $iv = str_repeat($zeroPack, 4);
        }
        $encryptedStr = trim(openssl_encrypt(trim($str), $method, $key, $options, $iv));
        switch ($format) {
            case self::FORMAT_HEX:
                return bin2hex($encryptedStr);
            case self::FORMAT_BASE64:
                return EncodeHelper::base64Encode($encryptedStr);
        }

        return bin2hex($encryptedStr);
    }

    /**
     * AES/CBC/PKCS5Padding Decrypter
     *
     * @param $encryptedStr
     * @param $key
     * @param string $method
     * @param int $options
     * @param string $iv
     * @param string $format
     * @return string
     */
    public static function opensslDecrypt(
        $encryptedStr,
        $key,
        $method = 'AES-256-CBC',
        $options = OPENSSL_RAW_DATA,
        $iv = self::ZERO_IV,
        $format = self::FORMAT_HEX
    )
    {
        if ($iv == self::ZERO_IV) {
            $zeroPack = pack('i*', 0);
            $iv = str_repeat($zeroPack, 4);
        }

        $encryptedStr = trim($encryptedStr);

        switch ($format) {
            case self::FORMAT_HEX:
                $encryptedStr = hex2bin($encryptedStr);
                break;
            case self::FORMAT_BASE64:
                $encryptedStr = EncodeHelper::base64Decode($encryptedStr);
                break;
            default:
                $encryptedStr = hex2bin($encryptedStr);
        }

        return trim(openssl_decrypt($encryptedStr, $method, $key, $options, $iv));
    }
}
