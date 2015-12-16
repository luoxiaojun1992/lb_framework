<?php
/**
 * Created by PhpStorm.
 * User: 224
 * Date: 2015/12/16
 * Time: 10:54
 */

namespace lb\tests\components\widget;

use lb\components\Security;
use lb\components\widget\Form;

class FormTest extends \PHPUnit_Framework_TestCase
{
    public function testRender()
    {
        $form_tpl = <<<Form
<form id="%s" method="%s" action="%s" class="%s" %s>
Form;
        $id = 'test_id';
        $method = 'post';
        $action = '/';
        $class = 'test';
        $attributes = [
            'enctype' => 'multipart/form-data',
        ];
        $attributes_code  = [];
        foreach ($attributes as $attribute => $value) {
            $attributes_code[] = implode('=', [$attribute, '"' . $value . '"']);
        }
        $expectedForm = sprintf($form_tpl, $id, $method, $action, $class, implode(' ', $attributes_code));

        $actualForm = Form::render($id, $method, $action, $class, $attributes);

        $this->assertEquals($expectedForm, $actualForm);
    }

    public function testEndForm()
    {
        $end_form_tpl = <<<EndForm
<input type="hidden" name="csrf_token" value="%s" />
</form>
EndForm;
        $csrf_token = Security::generateCsrfToken();
        $expectedEndForm = sprintf($end_form_tpl, $csrf_token);
        $actualEndForm = Form::endForm($csrf_token);
        $this->assertEquals($expectedEndForm, $actualEndForm);
    }
}
