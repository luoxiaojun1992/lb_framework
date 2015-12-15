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
}
