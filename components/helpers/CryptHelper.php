<?php

namespace lb\components\helpers;

use lb\BaseClass;
use Zend\Crypt\BlockCipher;
use Zend\Crypt\Symmetric\Mcrypt;

class CryptHelper extends BaseClass
{
    const ENCRYPT_METHOD_SUFFIX = 'encrypt';
    const DECRYPT_METHOD_SUFFIX = 'decrypt';

    protected static $block_cipher_instance = [];

    /**
     * @return string
     */
    protected static function mcrypt_get_iv()
    {
        //指定初始化向量iv的大小：
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);

        //创建初始化向量：
        return mcrypt_create_iv($iv_size, MCRYPT_RAND);
    }

    /**
     * @param $str
     * @param $key
     * @return string
     */
    public static function mcrypt_encrypt($str, $key)
    {
        $iv = static::mcrypt_get_iv();

        //加密后的内容：
        return EncodeHelper::base64Encode(trim(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, trim($str), MCRYPT_MODE_ECB, $iv)));
    }

    /**
     * @param $str
     * @param $key
     * @return string
     */
    public static function mcrypt_decrypt($str, $key)
    {
        $iv = static::mcrypt_get_iv();

        //解密后的内容：
        return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, EncodeHelper::base64Decode(trim($str)), MCRYPT_MODE_ECB, $iv));
    }

    /**
     * @param $key
     * @param string $algo
     * @return BlockCipher
     */
    protected static function zend_get_block_cipher($key, $algo = 'aes')
    {
        $instance_id = implode('@', [$key, $algo]);
        if (isset(static::$block_cipher_instance[$instance_id])) {
            $instance = static::$block_cipher_instance[$instance_id];
            if ($instance instanceof BlockCipher) {
                return $instance;
            }
        }
        $blockCipher = new BlockCipher(new Mcrypt(array('algo' => $algo)));
        $blockCipher->setKey($key);
        static::$block_cipher_instance[$instance_id] = $blockCipher;
        return $blockCipher;
    }

    /**
     * @param $str
     * @param $key
     * @param string $algo
     * @return mixed
     */
    public static function zend_encrypt($str, $key, $algo = 'aes')
    {
        $blockCipher = static::zend_get_block_cipher($key, $algo);
        return EncodeHelper::base64Encode(trim($blockCipher->encrypt(trim($str))));
    }

    /**
     * @param $str
     * @param $key
     * @param string $algo
     * @return mixed
     */
    public static function zend_decrypt($str, $key, $algo = 'aes')
    {
        $blockCipher = static::zend_get_block_cipher($key, $algo);
        return trim($blockCipher->decrypt(EncodeHelper::base64Decode(trim($str))));
    }

    /**
     * @param string $cryptor
     * @return string
     */
    public static function get_encrypt_method($cryptor = 'zend')
    {
        return implode('_', [$cryptor, static::ENCRYPT_METHOD_SUFFIX]);
    }

    /**
     * @param string $cryptor
     * @return string
     */
    public static function get_decrypt_method($cryptor = 'zend')
    {
        return implode('_', [$cryptor, static::DECRYPT_METHOD_SUFFIX]);
    }

    /**
     * RSA Encrypt
     *
     * @param $str
     * @param $publicKeyPath
     * @return string
     */
    public static function rsaEncrypt($str, $publicKeyPath)
    {
        openssl_public_encrypt(
            trim($str),
            $cryrted,
            openssl_get_publickey(file_get_contents($publicKeyPath))
        );
        return EncodeHelper::base64Encode(trim($cryrted));
    }

    /**
     * RSA Decrypt
     *
     * @param $str
     * @param $privateKeyPath
     * @return string
     */
    public static function rsaDecrypt($str, $privateKeyPath)
    {
        openssl_private_decrypt(
            EncodeHelper::base64Decode(trim($str)),
            $decrypted,
            openssl_get_privatekey(file_get_contents($privateKeyPath))
        );
        return trim($decrypted);
    }

    /**
     * RSA Sign
     *
     * @param $str
     * @param $privateKeyPath
     * @param int $algo
     * @return string
     */
    public static function sign($str, $privateKeyPath, $algo = OPENSSL_ALGO_SHA1)
    {
        $privateKey = openssl_pkey_get_private(file_get_contents($privateKeyPath));

        openssl_sign(trim($str), $singature, $privateKey, $algo);

        openssl_free_key($privateKey);

        return bin2hex(trim($singature));
    }

    /**
     * RSA Verify Sign
     *
     * @param $str
     * @param $sinature
     * @param $publicKeyPath
     * @param int $algo
     * @return bool
     */
    public static function verify($str, $sinature, $publicKeyPath, $algo = OPENSSL_ALGO_SHA1)
    {
        $publicKey = openssl_pkey_get_public(file_get_contents($publicKeyPath));

        $res = openssl_verify(trim($str), $sinature, $publicKey, $algo);

        openssl_free_key($publicKey);

        if ($res == 1) {
            return true;
        }

        return false;
    }
}
