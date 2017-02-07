<?php

namespace lb\components;

use lb\BaseClass;

class Pagination extends BaseClass
{
    public static function getParams($total, $page_size, $page = 1)
    {
        $pageTotal = ceil($total / $page_size);
        $offset = ($page - 1) * $page_size;
        if ($page != $pageTotal) {
            $limit = $page_size;
        } else {
            $limit = $total % $page_size ? : $page_size;
        }
        return [$offset, $limit];
    }
}
