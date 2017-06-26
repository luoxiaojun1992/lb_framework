<?php

namespace lb\tests\components\widget;

use lb\components\Security;
use lb\components\widget\Form;
use lb\tests\BaseTestCase;

class FormTest extends BaseTestCase
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

        $actualForm = Form::component()->setId($id)
            ->setMethod($method)
            ->setAction($action)
            ->setClass($class)
            ->setAttributes($attributes)
            ->render();

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
        $actualEndForm = Form::component()->setCsrfToken($csrf_token)->endForm();
        $this->assertEquals($expectedEndForm, $actualEndForm);
    }
}
