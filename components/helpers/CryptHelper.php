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
        return trim(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $str, MCRYPT_MODE_ECB, $iv));
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
        return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $str, MCRYPT_MODE_ECB, $iv));
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
        return trim($blockCipher->encrypt($str));
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
        return trim($blockCipher->decrypt($str));
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
}
