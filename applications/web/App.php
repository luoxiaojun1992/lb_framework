<?php

namespace lb\applications\web;

use lb\components\error_handlers\ParamException;
use lb\components\error_handlers\VariableException;
use lb\components\response\Response;
use lb\Lb;

class App extends Lb
{
    protected function handleRestException($exception)
    {
        Response::component()->response(
            [
                'code' => $exception->getCode(),
                'msg' => $exception->getMessage(),
            ], Response::RESPONSE_TYPE_JSON, false, $exception->getCode()
        );
    }

    protected function handleException($exception)
    {
        Lb::app()->error($exception->getTraceAsString());
        $status_code = $exception->getCode();
        if (Lb::app()->isRest()) {
            $this->handleRestException($exception);
        } else {
            Lb::app()->redirect(
                Lb::app()->createAbsoluteUrl(
                    '/web/action/error', [
                        'err_msg' => implode(':', [$status_code, $exception->getMessage()]),
                        'tpl_name' => 'error',
                        'status_code' => $status_code
                    ]
                )
            );
        }
    }

    protected function exitException(\Exception $exception)
    {
        Lb::app()->error($exception->getTraceAsString());

        if (Lb::app()->isRest()) {
            $this->handleRestException($exception);
        } else {
            Lb::app()->stop(implode(':', [$exception->getCode(), $exception->getMessage()]));
        }
    }

    public function __construct($is_single = false)
    {
        // Start App
        try {
            parent::__construct($is_single);
        } catch (VariableException $variableException) {
            $this->exitException($variableException);
        } catch (ParamException $paramException) {
            $this->exitException($paramException);
        } catch (\Throwable $throwable) {
            $this->handleException($throwable);
        }
    }

    public function run()
    {
        if (strtolower(php_sapi_name()) !== 'cli') {
            // Start App
            try {
                parent::run();
            } catch (VariableException $variableException) {
                $this->exitException($variableException);
            } catch (ParamException $paramException) {
                $this->exitException($paramException);
            } catch (\Throwable $throwable) {
                $this->handleException($throwable);
            }
        } else {
            Lb::app()->stop('Unsupported running mode.');
        }
    }
}
