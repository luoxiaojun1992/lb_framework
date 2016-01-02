<?php
/**
 * Created by PhpStorm.
 * User: luoxiaojun
 * Date: 16/1/2
 * Time: 下午3:42
 * Lb framework pagination component file
 */

namespace lb\components;

class Pagination
{
    public static function getParams($total, $page_size, $page = 1)
    {
        $pageTotal = ceil($total / $page_size);
        $offset = ($page - 1) * $page_size;
        if ($page != $pageTotal) {
            $limit = $page_size;
        } else {
            if ($pageTotal % $page_size) {
                $limit = $total % $page_size;
            } else {
                $limit = $page_size;
            }
        }
        return [$offset, $limit];
    }
}
