<?php
/**
 * Created by PhpStorm.
 * User: 224
 * Date: 2015/12/15
 * Time: 15:19
 */

namespace lb\tests\components\helpers;

use lb\components\helpers\FileHelper;

class FileHelperTest extends \PHPUnit_Framework_TestCase
{
    public function testGetExtensionName()
    {
        $file_path = __FILE__;
        $extension = 'php';
        $this->assertEquals($extension, FileHelper::getExtensionName($file_path));
    }
}
