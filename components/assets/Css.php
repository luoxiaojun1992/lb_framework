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
use lb\BaseClass;
use lb\Lb;

class Css extends BaseClass
{
    public static function dump($css_files)
    {
        $css_assets = [];
        foreach ($css_files as $css_file) {
            $css_assets[] = new FileAsset($css_file);
        }
        $css = new AssetCollection($css_assets);
        $css_html = $css->dump();
        $assets_cache_dir = Lb::app()->getRootDir() . DIRECTORY_SEPARATOR . 'assets/css';
        if (!is_dir($assets_cache_dir)) {
            mkdir($assets_cache_dir, 0777, true);
        }
        $assets_cache_name = md5(serialize($css_files));
        $assets_cache_path = $assets_cache_dir . DIRECTORY_SEPARATOR . $assets_cache_name;
        if (!file_exists($assets_cache_path)) {
            file_put_contents($assets_cache_path, $css_html);
        }
        return DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . $assets_cache_name;
    }
}
