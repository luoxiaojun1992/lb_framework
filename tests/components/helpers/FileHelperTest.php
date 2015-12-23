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

    public function testDownload()
    {
        $file_path = __FILE__;
        $fp = fopen($file_path, 'r');
        $file_size = filesize($file_path);
        $expected_content = fread($fp, $file_size);
        fclose($fp);

        ob_start();
        FileHelper::download($file_path, 'test');
        $actual_content = ob_get_contents();
        ob_end_clean();

        $this->assertEquals($expected_content, $actual_content);
    }

    public function testUpload()
    {

    }
}
