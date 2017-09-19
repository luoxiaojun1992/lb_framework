<?php

namespace lb\components\request;

use lb\BaseClass;
use lb\components\containers\Header;
use lb\components\error_handlers\ParamException;
use lb\components\helpers\ValidationHelper;

abstract class BaseRequest extends BaseClass implements RequestContract
{
    /** Validation Rules */
    const VALIDATE_REQUIRED = 'required';
    const VALIDATE_MOBILE = 'mobile';
    const VALIDATE_ENUM = 'enum';

    abstract public function getHeaders() : Header;

    /**
     * Get HTTP Header
     *
     * @param $headerKey
     * @return null
     */
    public function getHeader($headerKey)
    {
        return $this->getHeaders()->get($headerKey);
    }

    /**
     * 校验参数
     *
     * @param  array $rules
     * @param  array $errors
     * @throws ParamException
     */
    public function validate($rules = [], $errors = [])
    {
        foreach ($rules as $param => $subRules) {
            $paramValue = $this->getParam($param);
            foreach ($subRules as $key => $rule) {
                $isError = false;
                if (!is_array($rule)) {
                    switch ($rule) {
                        case self::VALIDATE_REQUIRED:
                            if (is_null($paramValue)) {
                                $isError = true;
                            }
                            break;
                        case self::VALIDATE_MOBILE:
                            if (!ValidationHelper::isMobile($paramValue)) {
                                $isError = true;
                            }
                            break;
                    }
                } else {
                    switch ($key) {
                    case self::VALIDATE_ENUM:
                        if (!in_array($paramValue, $rule)) {
                            $isError = true;
                        }
                        break;
                    }
                }
                if ($isError) {
                    $errCode = 500;
                    $errMsg = '';
                    if (is_array($rule)) {
                        $rule = $key;
                    }
                    if (isset($mergedErrors[$param][$rule]['code'])) {
                        $errCode = $mergedErrors[$param][$rule]['code'];
                    }
                    if (isset($mergedErrors[$param][$rule]['msg'])) {
                        $errMsg = str_replace('{param}', $param, $mergedErrors[$param][$rule]['msg']);
                    }
                    throw new ParamException($errMsg, $errCode);
                }
            }
        }
    }
}
