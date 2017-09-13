<?php

namespace lb\applications\console;

use lb\components\error_handlers\ConsoleException;
use lb\components\error_handlers\ParamException;
use lb\components\error_handlers\VariableException;
use lb\Lb;
use Monolog\Logger;

class App extends Lb
{
    protected function exitException($exception)
    {
        Lb::app()->error($exception->getTraceAsString());
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
                } catch (ConsoleException $consoleException) {
                    $this->exitException($consoleException);
                } catch (VariableException $variableException) {
                    $this->exitException($variableException);
                } catch (ParamException $paramException) {
                    $this->exitException($paramException);
                } catch (\Throwable $throwable) {
                    $this->exitException($throwable);
                }
            } else {
                // if php version < 7.0.0
                try {
                    parent::run();
                } catch (ConsoleException $consoleException) {
                    $this->exitException($consoleException);
                } catch (VariableException $variableException) {
                    $this->exitException($variableException);
                } catch (ParamException $paramException) {
                    $this->exitException($paramException);
                } catch (\Exception $e) {
                    $this->exitException($e);
                }
            }
        } else {
            Lb::app()->stop('Unsupported running mode.');
        }
    }
}
