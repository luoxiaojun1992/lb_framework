<?php
/**
 * Created by PhpStorm.
 * User: 224
 * Date: 2015/12/15
 * Time: 15:31
 */

namespace lb\tests\components\helpers;

use lb\components\helpers\HtmlHelper;

class HtmlHelperTest extends \PHPUnit_Framework_TestCase
{
    public function testCompress()
    {
        $html = <<<html
<html>
    <head></head>
    <body>
        <h1>test</h1>
    </body>
</html>
html;
        $compressedHtml = '<html><head></head><body><h1>test</h1></body></html>';
        $this->assertEquals($compressedHtml, HtmlHelper::compress($html));
    }

    public function testEncode()
    {
        $html = <<<html
<html>
    <head></head>
    <body>
        <h1>test</h1>
    </body>
</html>
html;
        $encodedHtml = htmlspecialchars($html);
        $this->assertEquals($encodedHtml, HtmlHelper::encode($html));
    }

    public function testDecode()
    {
        $html = <<<html
<html>
    <head></head>
    <body>
        <h1>test</h1>
    </body>
</html>
html;
        $encodedHtml = htmlspecialchars($html);
        $this->assertEquals($html, HtmlHelper::decode($encodedHtml));
    }
}
