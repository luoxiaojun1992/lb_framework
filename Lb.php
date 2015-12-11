<?php
/**
 * Created by PhpStorm.
 * User: luoxiaojun
 * Date: 15/11/6
 * Time: 上午11:31
 * Lb framework bootstrap file
 */

namespace lb;

use lb\components\error_handlers\HttpException;

class Lb extends \lb\BaseLb
{
    public function run()
    {
        if (strtolower(php_sapi_name()) !== 'cli') {
            // Start App
            if (class_exists('\EngineException')) {
                // PHP 7.0.0 +
                try {
                    parent::run();
                } catch (HttpException $httpException) {
                    Lb::app()->stop(implode(':', [$httpException->getCode(), $httpException->getMessage()]));
                } catch (\Exception $e) {
                    Lb::app()->stop(implode(':', [$e->getCode(), $e->getMessage()]));
                } catch (\EngineException $engineException) {
                    Lb::app()->stop(implode(':', [$engineException->getCode(), $engineException->getMessage()]));
                }
            } else {
                // PHP 7.0.0 -
                try {
                    parent::run();
                } catch (HttpException $httpException) {
                    Lb::app()->stop(implode(':', [$httpException->getCode(), $httpException->getMessage()]));
                } catch (\Exception $e) {
                    Lb::app()->stop(implode(':', [$e->getCode(), $e->getMessage()]));
                }
            }
        } else {
            echo 'Unsupported running mode.';
            die();
        }
    }
}
