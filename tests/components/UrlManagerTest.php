<?php

namespace lb\tests\components;

use lb\components\UrlManager;
use lb\tests\BaseTestCase;

class UrlManagerTest extends BaseTestCase
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
