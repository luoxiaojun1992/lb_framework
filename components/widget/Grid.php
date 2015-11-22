<?php
/**
 * Created by PhpStorm.
 * User: luoxiaojun
 * Date: 15/11/21
 * Time: 下午2:28
 * Lb framework grid widget file
 */

namespace lb\components\widget;

use lb\Lb;

class Grid extends Base
{
    public static function render($data_provider, $options, $htmlOptions = [])
    {
        $grid_tpl = <<<Grid
<table %s>
    <thead>%s</thead>
    <tbody>%s</tbody>
</table>
Grid;

        $tableHtmlOptions = [];
        if ($htmlOptions) {
            foreach ($htmlOptions as $attribute_name => $attribute_value) {
                $tableHtmlOptions[] = "$attribute_name=\"{$attribute_value}\"";
            }
        }
        $tableHtmlOptionHtml = implode(' ', $tableHtmlOptions);

        $thead_tpl = '<tr>%s</tr>';
        $thead = [];
        foreach ($options as $option) {
            if (isset($option['label'])) {
                $thead[] = "<td>{$option['label']}</td>";
            } else {
                $thead[] = '<td>Not Set</td>';
            }
        }
        $thead_html = sprintf($thead_tpl, implode('', $thead));

        $tbody = [];
        foreach ($data_provider as $data) {
            $tmpStr = '<tr>';
            foreach ($options as $option) {
                if (isset($option['attribute'])) {
                    $data_value = $data->{$option['attribute']};
                    if ($data_value !== false) {
                        $tmpStr .= "<td>{$data_value}</td>";
                    } else {
                        $tmpStr .= '<td>Not Set</td>';
                    }
                } else {
                    $tmpStr .= '<td>Not Set</td>';
                }
            }
            $tmpStr .= '</tr>';
            $tbody[] = $tmpStr;
        }
        $tbody_html = implode('', $tbody);

        $grid_html = sprintf($grid_tpl, $tableHtmlOptionHtml, $thead_html, $tbody_html);

        return $grid_html;
    }

    public static function getPagination($page, $data_total, $page_size = 10, $page_len = 10)
    {
        $pagination_tpl = <<<Pagination
<nav>
  <ul class="pagination">
    %s
  </ul>
</nav>
Pagination;

        $page_total = ceil($data_total / $page_size);
        if (!$page || !is_int($page) || $page < 1) {
            $page = 1;
        }
        if ($page > $page_total) {
            $page = $page_total;
        }

        //页码范围计算
        $init = 1;//起始页码数
        $max = $page_total;//结束页码数
        $pagelen = ($page_len % 2) ? $page_len : $page_len + 1;//页码个数
        $pageoffset = ($pagelen - 1) / 2;//页码个数左右偏移量
        //分页数大于页码个数时可以偏移
        if ($page_total > $pagelen) {
            //如果当前页小于等于左偏移
            if ($page <= $pageoffset) {
                $init = 1;
                $max = $pagelen;
            } else {//如果当前页大于左偏移
                //如果当前页码右偏移超出最大分页数
                if ($page + $pageoffset >= $page_total + 1) {
                    $init = $page_total - $pagelen + 1;
                } else {
                    //左右偏移都存在时的计算
                    $init = $page - $pageoffset;
                    $max = $page + $pageoffset;
                }
            }
        }

        $page_code = '';
        $uri = Lb::app()->getUri();
        if ($page > 1) {
            $page_code .= "<li>
      <a href=\"" . Lb::app()->createAbsoluteUrl($uri, ['page' => $page - 1]) . "\" aria-label=\"Previous\">
        <span aria-hidden=\"true\">&laquo;</span>
      </a>
    </li>";
        }
        for ($i = $init; $i <= $max; ++$i) {
            if ($page != $i) {
                $page_code .= "<li><a href=\"" . Lb::app()->createAbsoluteUrl($uri, ['page' => $i]) . "\">{$i}</a></li>";
            } else {
                $page_code .= "<li class=\"active\"><a href=\"" . Lb::app()->createAbsoluteUrl($uri, ['page' => $i]) . "\">{$i} <span class=\"sr-only\">(current)</span></a></li>";
            }
        }
        if ($page < $page_total) {
            $page_code .= "<li>
      <a href=\"" . Lb::app()->createAbsoluteUrl($uri, ['page' => $page + 1]) . "\" aria-label=\"Next\">
        <span aria-hidden=\"true\">&raquo;</span>
      </a>
    </li>";
        }

        return sprintf($pagination_tpl, $page_code);
    }
}
