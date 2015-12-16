<?php
/**
 * Created by PhpStorm.
 * User: 224
 * Date: 2015/12/16
 * Time: 14:31
 */

namespace lb\tests\components;

use lb\components\UrlManager;

class UrlManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateRelativeUrl()
    {
        $uri = '/index.php';
        $query_params = ['language' => 'zh_CN', 'id' => 1];
        $query_string = [];
        foreach ($query_params as $param => $value) {
            $query_string[] = implode('=', [$param, $value]);
        }
        $expectedRelativeUrl = $uri . '?' . implode('&', $query_string);
        $actualRelativeUrl = UrlManager::createRelativeUrl($uri, $query_params);
        $this->assertEquals($expectedRelativeUrl, $actualRelativeUrl);

        $expectedRelativeUrl = $uri;
        $actualRelativeUrl = UrlManager::createRelativeUrl($uri);
        $this->assertEquals($expectedRelativeUrl, $actualRelativeUrl);
    }
}
