<?php

namespace lb\tests\components\helpers;

use lb\components\helpers\FileHelper;
use lb\tests\BaseTestCase;

class FileHelperTest extends BaseTestCase
{
    public function testGetExtensionName()
    {
        $file_path = __FILE__;
        $extension = 'php';
        $this->assertEquals($extension, FileHelper::getExtensionName($file_path));
    }

    public function testFileExists()
    {
        $this->assertTrue(FileHelper::fileExists(__FILE__));
        $this->assertFalse(FileHelper::fileExists(__DIR__ . DIRECTORY_SEPARATOR . 'tmp'));
    }

    public function testDirExists()
    {
        $this->assertTrue(FileHelper::dirExists(__DIR__));
        $this->assertFalse(FileHelper::dirExists(__DIR__ . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR));
    }

    public function testResourceExists()
    {
        $this->assertTrue(FileHelper::resourceExists(__FILE__));
        $this->assertTrue(FileHelper::resourceExists(__DIR__));
        $this->assertFalse(FileHelper::resourceExists(__DIR__ . DIRECTORY_SEPARATOR . 'tmp'));
        $this->assertFalse(FileHelper::resourceExists(__DIR__ . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR));
    }
}
