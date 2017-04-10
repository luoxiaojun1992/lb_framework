<?php

namespace lb\components\helpers;

use lb\tests\BaseTestCase;

class EncodeHelperTest extends BaseTestCase
{
    public function testBase64Encode()
    {
        $str = 'test';
        $this->assertEquals($str, EncodeHelper::base64Decode(EncodeHelper::base64Encode($str)));
    }

    public function testUrlEncode()
    {
        $str = 'http://xxxxx?xxx=xxx';
        $this->assertEquals($str, EncodeHelper::urlDecode(EncodeHelper::urlEncode($str)));
    }
}
