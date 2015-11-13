<?php
/**
 * Created by PhpStorm.
 * User: 224
 * Date: 2015/11/13
 * Time: 17:55
 */

namespace lb\components\Assets;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Assetic\Filter\Yui\JsCompressorFilter as YuiCompressorFilter;

class Javascript
{
    public static function dump()
    {
        $js = new AssetCollection(array(
            new FileAsset(__DIR__.'/jquery.js'),
        ));

        header('Content-Type: application/js');
        echo $js->dump();
    }
}
