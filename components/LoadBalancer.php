<?php

namespace lb\components;

use lb\BaseClass;
use lb\components\distribution\FlexiHash;

class LoadBalancer extends BaseClass
{
    public static function getTargetHost($hosts)
    {
        if (!is_array($hosts)) {
            if (strpos($hosts, ',') !== false) {
                $hosts = explode(',', $hosts);
            } else {
                return null;
            }
        }

        return FlexiHash::component()->addServers($hosts)->lookup();
    }
}
