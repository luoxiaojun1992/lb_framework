<?php
/**
 * Created by PhpStorm.
 * User: luoxiaojun
 * Date: 15/11/21
 * Time: 下午2:28
 * Lb framework grid widget file
 */

namespace lb\components\widget;

class Grid extends Base
{
    public static function render($data_provider, $options)
    {
        $grid_tpl = <<<Grid
<table>
    <thead>%s</thead>
    <tbody>%s</tbody>
</table>
Grid;
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
                    if (isset($data->{$option['attribute']})) {
                        $tmpStr .= "<td>{$data->{$option['attribute']}}</td>";
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

        $grid_html = sprintf($grid_tpl, $thead_html, $tbody_html);

        return $grid_html;
    }
}
