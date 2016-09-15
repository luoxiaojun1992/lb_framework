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
            if (class_exists('\Throwable')) {
                // >= PHP 7.0.0
                try {
                    parent::run();
                } catch (HttpException $httpException) {
                    $status_code = $httpException->getCode();
                    $err_msg = implode(':', [$status_code, $httpException->getMessage()]);
                    Lb::app()->redirect(Lb::app()->createAbsoluteUrl('/web/action/error', ['err_msg' => $err_msg, 'tpl_name' => 'error', 'status_code' => $status_code]));
                } catch (\Throwable $throwable) {
                    $status_code = $throwable->getCode();
                    $err_msg = implode(':', [$status_code, $throwable->getMessage()]);
                    Lb::app()->redirect(Lb::app()->createAbsoluteUrl('/web/action/error', ['err_msg' => $err_msg, 'tpl_name' => 'error', 'status_code' => $status_code]));
                }
            } else {
                // < PHP 7.0.0
                try {
                    parent::run();
                } catch (HttpException $httpException) {
                    $status_code = $httpException->getCode();
                    $err_msg = implode(':', [$status_code, $httpException->getMessage()]);
                    Lb::app()->redirect(Lb::app()->createAbsoluteUrl('/web/action/error', ['err_msg' => $err_msg, 'tpl_name' => 'error', 'status_code' => $status_code]));
                } catch (\Exception $e) {
                    $status_code = $e->getCode();
                    $err_msg = implode(':', [$status_code, $e->getMessage()]);
                    Lb::app()->redirect(Lb::app()->createAbsoluteUrl('/web/action/error', ['err_msg' => $err_msg, 'tpl_name' => 'error', 'status_code' => $status_code]));
                }
            }
        } else {
            echo 'Unsupported running mode.';
            die();
        }
    }
}
