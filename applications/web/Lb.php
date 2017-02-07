<?php

namespace lb\applications\web;

use lb\components\error_handlers\HttpException;
use lb\components\error_handlers\VariableException;

class Lb extends \lb\BaseLb
{
    protected function handleException($exception)
    {
        $status_code = $exception->getCode();
        Lb::app()->redirect(Lb::app()->createAbsoluteUrl('/web/action/error', [
            'err_msg' => implode(':', [$status_code, $exception->getMessage()]),
            'tpl_name' => 'error',
            'status_code' => $status_code
        ]));
    }

    protected function exitException(\Exception $exception)
    {
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
                } catch (\Exception $e) {
                    $this->handleException($e);
                }
            }
        } else {
            echo 'Unsupported running mode.';
            die();
        }
    }
}
