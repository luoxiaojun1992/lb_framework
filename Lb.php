<?php
/**
 * Created by PhpStorm.
 * User: luoxiaojun
 * Date: 15/11/6
 * Time: 上午11:31
 * Lb framework bootstrap file
 */

namespace lb;

class Lb extends \lb\BaseLb
{
    public function run()
    {
        if (strtolower(php_sapi_name()) !== 'cli') {
            // Start App
            parent::run();
        } else {
            echo 'Unsupported running mode.';
            die();
        }
    }
}
