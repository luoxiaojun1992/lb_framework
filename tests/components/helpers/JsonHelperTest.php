<?php
/**
 * Created by PhpStorm.
 * User: 224
 * Date: 2015/12/15
 * Time: 13:39
 */

namespace lb\tests\components\helpers;

use lb\components\helpers\JsonHelper;

class JsonHelperTest extends \PHPUnit_Framework_TestCase
{
    public function testEncode()
    {
        $array = [
            'name' => 'a',
            'age' => 23,
            ['name' => 'a', 'age' => 23],
            [
                ['name' => 'a', 'age' => 23],
                ['name' => 'b', 'age' => 24],
            ],
        ];
        $notArray = 'name a age 23';
        $arrayExpectedJson = json_encode($array, JSON_UNESCAPED_UNICODE);
        $notArrayExpectedJson = json_encode([$notArray], JSON_UNESCAPED_UNICODE);

        $this->assertEquals($arrayExpectedJson, JsonHelper::encode($array));
        $this->assertEquals($notArrayExpectedJson, JsonHelper::encode($notArray));
    }

    public function testDecode()
    {
        $array = [
            'name' => 'a',
            'age' => 23,
            ['name' => 'a', 'age' => 23],
            [
                ['name' => 'a', 'age' => 23],
                ['name' => 'b', 'age' => 24],
            ],
        ];
        $arrayJson = json_encode($array, JSON_UNESCAPED_UNICODE);
        $notArray = 'name a age 23';

        $this->assertEquals($array, JsonHelper::decode($arrayJson));
        $this->assertEquals([$notArray], JsonHelper::decode($notArray));
    }

    public function testIsJson()
    {
        $array = [
            'name' => 'a',
            'age' => 23,
            ['name' => 'a', 'age' => 23],
            [
                ['name' => 'a', 'age' => 23],
                ['name' => 'b', 'age' => 24],
            ],
        ];
        $arrayJson = json_encode($array, JSON_UNESCAPED_UNICODE);
        $notArray = 'name a age 23';

        $this->assertTrue(JsonHelper::is_json($arrayJson));
        $this->assertFalse(JsonHelper::is_json($notArray));
    }
}
