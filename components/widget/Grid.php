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
                    if ($data_value) {
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
}
