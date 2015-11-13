<?php
/**
 * Created by PhpStorm.
 * User: luoxiaojun
 * Date: 15/11/13
 * Time: 下午10:01
 * Lb framework css asset file
 */

namespace lb\components\assets;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;

class Css
{
    public static function dump($css_files)
    {
        $css_assets = [];
        foreach ($css_files as $css_file) {
            $css_assets[] = new FileAsset($css_file);
        }
        $css = new AssetCollection($css_assets);

        return $css->dump();
    }
}
