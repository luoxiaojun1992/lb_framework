<?php
/**
 * Created by PhpStorm.
 * User: 224
 * Date: 2015/7/30
 * Time: 11:26
 */

namespace lb\tests\components\helpers;

use lb\components\helpers\CryptHelper;

class CryptHelperTest extends \PHPUnit_Framework_TestCase
{
    protected $str;
    protected $key;

    public function setUp()
    {
        $this->str = 'test string';
        $this->key = 'test key';
    }

    public function tearDown()
    {

    }

    public function testMcryptCryptor()
    {
        $expectedStr = $this->str;
        $encrypted_str = CryptHelper::mcrypt_encrypt($expectedStr, $this->key);
        $actualStr = CryptHelper::mcrypt_decrypt($encrypted_str, $this->key);
        $this->assertEquals($expectedStr, $actualStr);
    }

    public function testZendCryptor()
    {
        $expectedStr = $this->str;
        $encrypted_str = CryptHelper::zend_encrypt($expectedStr, $this->key);
        $actualStr = CryptHelper::zend_decrypt($encrypted_str, $this->key);
        $this->assertEquals($expectedStr, $actualStr);
    }
}
