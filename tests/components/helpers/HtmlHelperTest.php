<?php

namespace lb\tests\components\helpers;

use lb\components\helpers\HtmlHelper;
use lb\tests\BaseTestCase;

class HtmlHelperTest extends BaseTestCase
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

    public function testImage()
    {
        $src = 'http://www.baidu.com/test';
        $alt = 'test';
        $options = [
            'id' => 'test',
            'class' => 'test',
        ];
        $expectedImageTag = '<img src="' . $src . '" alt="' . $alt . '" id="test" class="test" />';
        $actualImageTag = HtmlHelper::image($src, $alt, $options);
        $this->assertEquals($expectedImageTag, $actualImageTag);
    }

    public function testA()
    {
        $href = 'http://www.baidu.com/test';
        $content = 'test';
        $title = 'test';
        $target = '_blank';
        $options = [
            'id' => 'test',
            'class' => 'test',
        ];
        $expectedATag = '<a href="' . $href . '" title="' . $title . '" target="' . $target . '" id="test" class="test">' . $content . '</a>';
        $actualATag = HtmlHelper::a($href, $content, $title, $target, $options);
        $this->assertEquals($expectedATag, $actualATag);
    }

    public function testPurify()
    {
        $html = <<<html
<h1>test</h1>
<p>test</p>
<script></script>
html;
        $purifiedHtml = <<<html
<h1>test</h1>
<p>test</p>
html;
        $this->assertEquals($purifiedHtml, HtmlHelper::purify($html));
    }
}
