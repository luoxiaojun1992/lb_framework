<?php

namespace lb\components\traits\lb;

trait Cache
{
    use FileCache;
    use Memcache;
    use Redis;
}
