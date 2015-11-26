<?php
/**
 * Created by PhpStorm.
 * User: 224
 * Date: 2015/11/26
 * Time: 15:28
 * Lb framework form widget file
 */

namespace lb\components\widget;

class Form extends Base
{
    public static function render($id = '', $method = 'post', $action = '', $class = '', $attributes = [])
    {
        $form_tpl = <<<Form
<form id="%s" method="%s" action="%s" class="%s" %s>
Form;
        $otherAttributes = [];
        if ($attributes) {
            foreach ($attributes as $attribute_name => $attribute_value) {
                $otherAttributes[] = implode('=', [$attribute_name, '"' . $attribute_value . '"']);
            }
        }
        return sprintf($form_tpl, $id, $method, $action, $class, implode(' ', $otherAttributes));
    }
}
