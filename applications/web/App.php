<?php

namespace lb\applications\web;

use lb\components\error_handlers\HttpException;
use lb\components\error_handlers\ParamException;
use lb\components\error_handlers\VariableException;
use lb\Lb;
use Monolog\Logger;

class App extends Lb
{
    protected function handleException($exception)
    {
        Lb::app()->log($exception->getTraceAsString(), [], Logger::ERROR);
        $status_code = $exception->getCode();
        Lb::app()->redirect(Lb::app()->createAbsoluteUrl('/web/action/error', [
            'err_msg' => implode(':', [$status_code, $exception->getMessage()]),
            'tpl_name' => 'error',
            'status_code' => $status_code
        ]));
    }

    protected function exitException(\Exception $exception)
    {
        Lb::app()->log($exception->getTraceAsString(), [], Logger::ERROR);
        Lb::app()->stop(implode(':', [$exception->getCode(), $exception->getMessage()]));
    }

    public function run()
    {
        if (strtolower(php_sapi_name()) !== 'cli') {
            // Start App
            if (class_exists('\Throwable')) {
                // if php version >= 7.0.0
                try {
                    parent::run();
                } catch (HttpException $httpException) {
                    $this->handleException($httpException);
                } catch (VariableException $variableException) {
                    $this->exitException($variableException);
                } catch (ParamException $paramException) {
                    $this->exitException($paramException);
                } catch (\Throwable $throwable) {
                    $this->handleException($throwable);
                }
            } else {
                // if php version < 7.0.0
                try {
                    parent::run();
                } catch (HttpException $httpException) {
                    $this->handleException($httpException);
                } catch (VariableException $variableException) {
                    $this->exitException($variableException);
                } catch (ParamException $paramException) {
                    $this->exitException($paramException);
                } catch (\Exception $e) {
                    $this->handleException($e);
                }
            }
        } else {
            Lb::app()->stop('Unsupported running mode.');
        }
    }
}
