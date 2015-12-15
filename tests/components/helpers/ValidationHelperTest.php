<?php
/**
 * Created by PhpStorm.
 * User: 224
 * Date: 2015/12/15
 * Time: 14:20
 */

namespace lb\tests\components\helpers;

use lb\components\helpers\ValidationHelper;

class ValidationHelperTest extends \PHPUnit_Framework_TestCase
{
    public function testIsUrl()
    {
        $httpUrl = 'http://www.baidu.com/';
        $httpsUrl = 'https://www.baidu.com/';
        $notUrl = 'www.baidu.com';

        $this->assertTrue(ValidationHelper::isUrl($httpUrl));
        $this->assertTrue(ValidationHelper::isUrl($httpsUrl));
        $this->assertFalse(ValidationHelper::isUrl($notUrl));
    }

    public function testIsRequired()
    {
        $value = 'test value';
        $this->assertTrue(ValidationHelper::isRequired($value));
        $this->assertFalse(ValidationHelper::isRequired(''));
    }

    public function testIsEmail()
    {
        $emailAddress = 'admin@aocs.com.cn';
        $notEmailAddress = 'www.aocs.com.cn';
        $this->assertTrue(ValidationHelper::isEmail($emailAddress));
        $this->assertFalse(ValidationHelper::isEmail($notEmailAddress));
    }

    public function testIsIp()
    {
        $ip4 = '192.168.0.1';
        $notIp = '192.168.0';
        $this->assertTrue(ValidationHelper::isIP($ip4));
        $this->assertFalse(ValidationHelper::isIP($notIp));
    }
}
