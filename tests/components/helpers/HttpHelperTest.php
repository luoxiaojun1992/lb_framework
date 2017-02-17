<?php

namespace lb\tests\components\helpers;

use lb\components\helpers\HttpHelper;
use lb\tests\BaseTestCase;

class HttpHelperTest extends BaseTestCase
{
    protected $ext;
    protected $mime_type;
    protected $status_code;
    protected $status_code_message;

    public function setUp()
    {
        parent::setUp();

        $this->ext = 'apk';
        $this->mime_type = 'application/vnd.android.package-archive';
        $this->status_code = 200;
        $this->status_code_message = 'OK';
    }

    public function testGetMimeType()
    {
        $actualMimeType = HttpHelper::get_mime_type($this->ext);
        $this->assertEquals($this->mime_type, $actualMimeType);
    }

    public function testGetExt()
    {
        $actualExt = HttpHelper::get_ext($this->mime_type);
        $this->assertEquals($this->ext, $actualExt);
    }

    public function testGetStatusCodeMessage()
    {
        $this->assertEquals($this->status_code_message, HttpHelper::get_status_code_message($this->status_code));
    }
}
