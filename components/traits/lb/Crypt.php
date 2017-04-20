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
     * @return mixed|string
     */
    public function encrypt($str, $key)
    {
        if ($this->isSingle()) {
            return CryptHelper::opensslEncrypt($str, $key);
        }
        return '';
    }

    /**
     * Decrypt
     *
     * @param $str
     * @param $key
     * @return mixed|string
     */
    public function decrypt($str, $key)
    {
        if ($this->isSingle()) {
            return CryptHelper::opensslDecrypt($str, $key);
        }
        return '';
    }

    /**
     * Encrypt By Config
     *
     * @param $str
     * @return mixed|string
     */
    public function encryptByConfig($str)
    {
        if ($this->isSingle()) {
            if ($security_key = $this->getConfigByName('security_key')) {
                $str = $this->encrypt($str, hex2bin(md5($security_key)));
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
    public function decryptByConfig($str)
    {
        if ($this->isSingle()) {
            if ($security_key = $this->getConfigByName('security_key')) {
                $str = $this->decrypt($str, hex2bin(md5($security_key)));
            }
            return $str;
        }
        return '';
    }
}
