<?php

namespace lb\components\contracts;

use lb\components\containers\Header;

interface RequestContract
{
    public function getClientAddress();

    public function getHost();

    public function getUri();

    public function getHostAddress();

    public function getUserAgent();

    public function getRequestMethod();

    public function getQueryString();

    public function getReferer();

    public function isAjax();

    public function getBasicAuthUser();

    public function getBasicAuthPassword();

    public function getHeaders() : Header;

    public function getHeader($headerKey);

    public function getParam($param_name, $default_value = null);

    public function getRawContent();
}
