<?php

namespace lb\applications\swoole;

use lb\components\error_handlers\ParamException;
use lb\components\error_handlers\VariableException;
use lb\components\response\SwooleResponse;
use lb\Lb;
use lb\SwooleLb;

class App extends SwooleLb
{
    protected function handleRestException($exception)
    {
        $this->response->response(
            [
                'code' => $exception->getCode(),
                'msg' => $exception->getMessage(),
            ], SwooleResponse::RESPONSE_TYPE_JSON, false, $exception->getCode()
        );
    }

    protected function handleException($exception)
    {
        Lb::app()->error($exception->getTraceAsString());
        $status_code = $exception->getCode();
        if ($this->isRest()) {
            $this->handleRestException($exception);
        } else {
            Lb::app()->redirect(
                Lb::app()->createAbsoluteUrl(
                    '/web/action/error', [
                    'err_msg' => implode(':', [$status_code, $exception->getMessage()]),
                    'tpl_name' => 'error',
                    'status_code' => $status_code
                    ]
                ), true, null, $this->response
            );
        }
    }

    protected function exitException(\Exception $exception)
    {
        Lb::app()->error($exception->getTraceAsString());
        if ($this->isRest()) {
            $this->handleRestException($exception);
        } else {
            $this->response->getSwooleResponse()->end(implode(':', [$exception->getCode(), $exception->getMessage()]));
        }
    }

    public function __construct($request, $response)
    {
        // Start App
        try {
            parent::__construct($request, $response);
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
        if (strtolower(php_sapi_name()) === 'cli') {
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
            $this->response->getSwooleResponse()->end('Unsupported running mode.');
        }
    }
}
