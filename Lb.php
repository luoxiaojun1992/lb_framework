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
                    $err_msg = implode(':', [$httpException->getCode(), $httpException->getMessage()]);
                    Lb::app()->redirect(Lb::app()->createAbsoluteUrl('/web/action/error', ['err_msg' => $err_msg, 'tpl_name' => 'error']));
                } catch (\Exception $e) {
                    $err_msg = implode(':', [$e->getCode(), $e->getMessage()]);
                    Lb::app()->redirect(Lb::app()->createAbsoluteUrl('/web/action/error', ['err_msg' => $err_msg, 'tpl_name' => 'error']));
                } catch (\EngineException $engineException) {
                    $err_msg = implode(':', [$engineException->getCode(), $engineException->getMessage()]);
                    Lb::app()->redirect(Lb::app()->createAbsoluteUrl('/web/action/error', ['err_msg' => $err_msg, 'tpl_name' => 'error']));
                }
            } else {
                // PHP 7.0.0 -
                try {
                    parent::run();
                } catch (HttpException $httpException) {
                    $err_msg = implode(':', [$httpException->getCode(), $httpException->getMessage()]);
                    Lb::app()->redirect(Lb::app()->createAbsoluteUrl('/web/action/error', ['err_msg' => $err_msg, 'tpl_name' => 'error']));
                } catch (\Exception $e) {
                    $err_msg = implode(':', [$e->getCode(), $e->getMessage()]);
                    Lb::app()->redirect(Lb::app()->createAbsoluteUrl('/web/action/error', ['err_msg' => $err_msg, 'tpl_name' => 'error']));
                }
            }
        } else {
            echo 'Unsupported running mode.';
            die();
        }
    }
}
