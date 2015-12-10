<?php
/**
 * Created by PhpStorm.
 * User: 224
 * Date: 2015/11/13
 * Time: 17:55
 * Lb framework javascript asset file
 */

namespace lb\components\assets;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Assetic\Filter\JSMinFilter;
use lb\Lb;

class Javascript
{
    public static function dump($js_files)
    {
        $js_assets = [];
        foreach ($js_files as $js_file) {
            $js_assets[] = new FileAsset($js_file);
        }
        $js = new AssetCollection($js_assets, new JSMinFilter());
        $js_html = $js->dump();
        $assets_cache_dir = Lb::app()->getRootDir() . DIRECTORY_SEPARATOR . 'assets/js';
        if (!is_dir($assets_cache_dir)) {
            mkdir($assets_cache_dir, 0777, true);
        }
        $assets_cache_name = md5(serialize($js_files));
        $assets_cache_path = $assets_cache_dir . DIRECTORY_SEPARATOR . $assets_cache_name;
        if (!file_exists($assets_cache_path)) {
            file_put_contents($assets_cache_path, $js_html);
        }
        return DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . $assets_cache_name;
    }
}
