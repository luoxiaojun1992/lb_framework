<?php
/**
 * Created by PhpStorm.
 * User: 224
 * Date: 2015/12/15
 * Time: 14:01
 */

namespace lb\tests\components\helpers;

use lb\components\helpers\XMLHelper;

class XMLHelperTest extends \PHPUnit_Framework_TestCase
{
    const XML_TPL = '<?xml version="1.0" encoding="UTF-8" ?>';

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
        $arrayExpectedXML = static::XML_TPL . '<name>a</name><age>23</age><0><name>a</name><age>23</age></0><1><0><name>a</name><age>23</age></0><1><name>b</name><age>24</age></1></1>';
        $notArrayExpectedXML = static::XML_TPL . "<0>{$notArray}</0>";

        $this->assertEquals($arrayExpectedXML, XMLHelper::encode($array));
        $this->assertEquals($notArrayExpectedXML, XMLHelper::encode($notArray));
    }
}
