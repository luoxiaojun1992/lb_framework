<?php

namespace lb\components\widget;

use lb\components\traits\Singleton;

class Form extends Base
{
    use Singleton;

    const FORM_TPL = <<<Form
<form id="%s" method="%s" action="%s" class="%s" %s>
Form;

    const END_FORM_TPL = <<<EndForm
<input type="hidden" name="csrf_token" value="%s" />
</form>
EndForm;

    protected $id = '';
    protected $method = 'post';
    protected $action = '';
    protected $class = '';
    protected $attributes = [];
    protected $csrfToken = '';

    public function init()
    {
        $this->setId('');
        $this->setMethod('post');
        $this->setAction('');
        $this->setClass('');
        $this->setAttributes([]);
        $this->setCsrfToken('');
    }

    /**
     * @return object
     */
    public static function component()
    {
        if (static::$instance instanceof static) {
            $instance = static::$instance;
            $instance->init();
            return $instance;
        } else {
            return (static::$instance = new static());
        }
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return $this;
     */
    public function setId(string $id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param string $method
     * @return $this;
     */
    public function setMethod(string $method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @return string
     */
    public function getCsrfToken(): string
    {
        return $this->csrfToken;
    }

    /**
     * @param string $csrfToken
     * @return $this;
     */
    public function setCsrfToken(string $csrfToken)
    {
        $this->csrfToken = $csrfToken;
        return $this;
    }

    /**
     * @param string $action
     * @return $this;
     */
    public function setAction(string $action)
    {
        $this->action = $action;
        return $this;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @param string $class
     * @return $this;
     */
    public function setClass(string $class)
    {
        $this->class = $class;
        return $this;
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param array $attributes
     * @return $this;
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }

    public function render()
    {
        $otherAttributes = [];
        if ($this->getAttributes()) {
            foreach ($this->getAttributes() as $attribute_name => $attribute_value) {
                $otherAttributes[] = implode('=', [$attribute_name, '"' . $attribute_value . '"']);
            }
        }
        return sprintf(
            static::FORM_TPL,
            $this->getId(),
            $this->getMethod(),
            $this->getAction(),
            $this->getClass(),
            implode(' ', $otherAttributes)
        );
    }

    public function endForm()
    {
        return sprintf(static::END_FORM_TPL, $this->getCsrfToken());
    }
}
