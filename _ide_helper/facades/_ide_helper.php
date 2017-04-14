<?php

class RedisKit extends \lb\components\cache\Redis
{
    //
}

class MemcacheKit extends \lb\components\cache\Memcache
{
    //
}

class FilecacheKit extends \lb\components\cache\Filecache
{
    //
}

class RequestKit extends \lb\components\request\Request
{
    //
}

class ResponseKit extends \lb\components\response\Response
{
    //
}

class HelperLb extends \lb\Lb
{
    use \lb\components\traits\lb\FileCache;
    use \lb\components\traits\lb\Memcache;
    use \lb\components\traits\lb\Redis;
}
