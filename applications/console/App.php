<?php

namespace lb\applications\console;

use lb\components\error_handlers\HttpException;
use lb\components\error_handlers\VariableException;
use lb\Lb;

class App extends Lb
{
    protected function exitException($exception)
    {
        dd(implode(':', [$exception->getCode(), $exception->getMessage()]));
    }

    public function run()
    {
        if (strtolower(php_sapi_name()) === 'cli') {
            // Start App
            if (class_exists('\Throwable')) {
                // if php version >= 7.0.0
                try {
                    parent::run();
                } catch (HttpException $httpException) {
                    $this->exitException($httpException);
                } catch (VariableException $variableException) {
                    $this->exitException($variableException);
                } catch (\Throwable $throwable) {
                    $this->exitException($throwable);
                }
            } else {
                // if php version < 7.0.0
                try {
                    parent::run();
                } catch (HttpException $httpException) {
                    $this->exitException($httpException);
                } catch (VariableException $variableException) {
                    $this->exitException($variableException);
                } catch (\Exception $e) {
                    $this->exitException($e);
                }
            }
        } else {
            echo 'Unsupported running mode.';
            die();
        }
    }
}
