<?php

namespace lb\components\traits\lb;

use lb\components\helpers\CryptHelper;

trait Crypt
{
    /**
     * Encrype
     *
     * @param $str
     * @param $key
     * @param string $cryptor
     * @param string $algo
     * @return mixed|string
     */
    public function encrypt($str, $key, $cryptor = 'zend', $algo = 'aes')
    {
        if ($this->isSingle()) {
            $encrypt_method = CryptHelper::get_encrypt_method($cryptor);
            return call_user_func_array([CryptHelper::className(), $encrypt_method], [$str, $key, $algo]);
        }
        return '';
    }

    /**
     * Decrypt
     *
     * @param $str
     * @param $key
     * @param string $cryptor
     * @param string $algo
     * @return mixed|string
     */
    public function decrypt($str, $key, $cryptor = 'zend', $algo = 'aes')
    {
        if ($this->isSingle()) {
            $decrypt_method = CryptHelper::get_decrypt_method($cryptor);
            return call_user_func_array([CryptHelper::className(), $decrypt_method], [$str, $key, $algo]);
        }
        return '';
    }

    /**
     * Encrypt By Config
     *
     * @param $str
     * @return mixed|string
     */
    public function encrypt_by_config($str)
    {
        if ($this->isSingle()) {
            $security_key = $this->getConfigByName('security_key');
            if ($security_key) {
                $cryptor = $this->getConfigByName('cryptor');
                if (!$cryptor) {
                    $cryptor = 'zend';
                }
                $str = $this->encrypt($str, $security_key, $cryptor);
            }
            return $str;
        }
        return '';
    }

    /**
     * Decrypt By Config
     *
     * @param $str
     * @return mixed|string
     */
    public function decrypt_by_config($str)
    {
        if ($this->isSingle()) {
            $security_key = $this->getConfigByName('security_key');
            if ($security_key) {
                $cryptor = $this->getConfigByName('cryptor');
                if (!$cryptor) {
                    $cryptor = 'zend';
                }
                $str = $this->decrypt($str, $security_key, $cryptor);
            }
            return $str;
        }
        return '';
    }
}
