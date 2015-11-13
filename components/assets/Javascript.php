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

class Javascript
{
    public static function dump($js_files)
    {
        $js_assets = [];
        foreach ($js_files as $js_file) {
            $js_assets[] = new FileAsset($js_file);
        }
        $js = new AssetCollection($js_assets);

        header('Content-Type: application/js');
        return $js->dump();
    }
}
