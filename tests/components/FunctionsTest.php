<?php
/**
 * Created by PhpStorm.
 * User: 224
 * Date: 2016/8/2
 * Time: 14:50
 */

namespace lb\tests\components;

class FunctionsTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();

        require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'Functions.php');
    }

    public function testEcho()
    {
        $var = 'test';
        ob_start();
        @_echo($var);
        $content = ob_get_contents();
        ob_end_clean();
        $this->assertEquals($var, $content);

        ob_start();
        @_echo($var_not_exists);
        $content = ob_get_contents();
        ob_end_clean();
        $this->assertEmpty($content);
    }

    public function testEnv()
    {
        $this->assertNotEmpty(env('PATH'));
    }
}
