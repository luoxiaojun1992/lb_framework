<?php

namespace lb\tests\components\helpers;

use lb\components\helpers\CryptHelper;
use lb\tests\BaseTestCase;

class CryptHelperTest extends BaseTestCase
{
    protected $str1;
    protected $str2;
    protected $key;

    public function setUp()
    {
        parent::setUp();

        $this->str1 = 'test string 1';
        $this->str2 = 'test str 2';
        $this->key = hex2bin(md5('q@e5c8%zM3LDb$4l'));
    }

    public function testOpenSSLEncrypt()
    {
        $expectedStr = $this->str1;
        $encrypted_str = CryptHelper::opensslEncrypt($expectedStr, $this->key);
        $actualStr = CryptHelper::opensslDecrypt($encrypted_str, $this->key);
        $this->assertEquals($expectedStr, $actualStr);

        $expectedStr = $this->str2;
        $encrypted_str = CryptHelper::opensslEncrypt($expectedStr, $this->key);
        $actualStr = CryptHelper::opensslDecrypt($encrypted_str, $this->key);
        $this->assertEquals($expectedStr, $actualStr);
    }
}
