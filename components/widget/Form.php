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
    const FORM_TPL = <<<Form
<form id="%s" method="%s" action="%s" class="%s" %s>
Form;

    const END_FORM_TPL = <<<EndForm
<input type="hidden" name="%s" value="%s" />
<input type="hidden" name="csrf_token" value="%s" />
</form>
EndForm;

    public static function render($id = '', $method = 'post', $action = '', $class = '', $attributes = [])
    {
        $otherAttributes = [];
        if ($attributes) {
            foreach ($attributes as $attribute_name => $attribute_value) {
                $otherAttributes[] = implode('=', [$attribute_name, '"' . $attribute_value . '"']);
            }
        }
        return sprintf(self::FORM_TPL, $id, $method, $action, $class, implode(' ', $otherAttributes));
    }

    public static function endForm($controller_id = '', $csrf_token = '')
    {
        return sprintf(self::END_FORM_TPL, $controller_id, $controller_id, $csrf_token);
    }
}
