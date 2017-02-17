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
}
