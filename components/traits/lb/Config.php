<?php

namespace lb\components\traits\lb;

use lb\Lb;
use lb\components\containers\Config as ConfigContainer;

trait Config
{
    // Get App Root Directory
    public function getRootDir()
    {
        return $this->getConfigByName('root_dir');
    }

    // Get App Name
    public function getName()
    {
        return $this->getConfigByName('name');
    }

    // Get Restful Api Config
    public function getRest()
    {
        return $this->getConfigByName('rest');
    }

    // Get Http Port
    public function getHttpPort()
    {
        return $this->getConfigByName('http_port');
    }

    // Get Time Zone
    public function getTimeZone()
    {
        return $this->getConfigByName('timeZone');
    }

    // Get mb internal encoding configuration
    public function getMbInternalEncoding()
    {
        return $this->getConfigByName('mb_internal_encoding');
    }

    // Get Cdn Host
    public function getCdnHost()
    {
        return trim((string)$this->getConfigByName('cdn_host'), '/');
    }

    // Get Seo Settings
    public function getSeo()
    {
        return $this->getConfigByName('seo');
    }

    // Get Custom Configuration
    public function getCustomConfig($name = '')
    {
        $custom_config = $this->getConfigByName('custom');
        return $name ? ($custom_config[$name] ?? null) : $custom_config;
    }

    // Get Home Controller & Action
    public function getHome()
    {
        return $this->getConfigByName('home');
    }

    // Get DB Config
    public function getDbConfig($db_type)
    {
        return $this->getConfigByName($db_type);
    }

    // Get Csrf Config
    public function getCsrfConfig()
    {
        return $this->getConfigByName('csrf');
    }

    // Get RPC Config
    public function getRpcConfig()
    {
        return $this->getConfigByName('rpc');
    }

    // Get Api Doc Config
    public function getApiDocConfig()
    {
        return $this->getConfigByName('api_doc');
    }

    // Get Log Config
    public function getLogConfig()
    {
        return $this->getConfigByName('log');
    }

    // Get Swoole Config
    public function getSwooleConfig()
    {
        return $this->getConfigByName('swoole') ? : [];
    }

    // Get Configuration By Name
    public function getConfigByName($config_name)
    {
        if ($this->isSingle()) {
            if (isset($this->containers['config'])) {
                return $this->containers['config']->get($config_name);
            }
        }
        return [];
    }

    // Get Url Manager Config By Item Name
    public function getUrlManagerConfig($item)
    {
        $urlManager = $this->getConfigByName('urlManager');
        if (isset($urlManager[$item])) {
            return $urlManager[$item];
        }
        return false;
    }

    // Is Pretty Url
    public function isPrettyUrl()
    {
        return $this->getUrlManagerConfig('is_pretty_url');
    }

    // Get Custom Url Suffix
    public function getUrlSuffix()
    {
        return $this->getUrlManagerConfig('suffix') ? : '';
    }

    // Get Js Files
    public function getJsFiles($controller_id, $template_id)
    {
        $js_files = [];
        $asset_config = $this->getConfigByName('assets');
        if (isset($asset_config[$controller_id][$template_id]['js'])) {
            $js_files = $asset_config[$controller_id][$template_id]['js'];
        }
        return $js_files;
    }

    // Get Css Files
    public function getCssFiles($controller_id, $template_id)
    {
        $css_files = [];
        $asset_config = $this->getConfigByName('assets');
        if (isset($asset_config[$controller_id][$template_id]['css'])) {
            $css_files = $asset_config[$controller_id][$template_id]['css'];
        }
        return $css_files;
    }

    /**
     * Get Queue Config
     *
     * @return array
     */
    public function getQueueConfig()
    {
        return $this->getConfigByName('queue');
    }

    /**
     * Get Id Generator Config
     *
     * @return array
     */
    public function getIdGeneratorConfig()
    {
        return $this->getConfigByName('id_generator');
    }

    /**
     * Get Facades Config
     *
     * @return array
     */
    public function getFacadesConfig()
    {
        return $this->getConfigByName('facades') ? : [];
    }

    /**
     * Get session config
     *
     * @return array
     */
    public function getSessionConfig()
    {
        return $this->getConfigByName('session') ? : [];
    }

    /**
     * Get login required filter
     *
     * @return array
     */
    public function getLoginRequiredFilter()
    {
        return $this->getConfigByName('login_required_filter') ? : [];
    }

    /**
     * Get login default url
     *
     * @return array
     */
    public function getLoginDefaultUrl()
    {
        return $this->getConfigByName('login_default_url') ? : [];
    }

    /**
     * If login required or not
     *
     * @return bool
     */
    public function isLoginRequired()
    {
        return $this->getConfigByName('login_required') ? : false;
    }

    /**
     * Get http cache config
     *
     * @return array
     */
    public function getHttpCacheConfig()
    {
        return $this->getConfigByName('http_cache') ? : [];
    }

    /**
     * Get page cache config
     *
     * @return array
     */
    public function getPageCacheConfig()
    {
        return $this->getConfigByName('page_cache') ? : [];
    }

    /**
     * Get page compress config
     *
     * @return array
     */
    public function getPageCompressConfig()
    {
        return $this->getConfigByName('page_compress') ? : [];
    }

    /**
     * Init Configuration
     */
    protected function initConfig()
    {
        if (defined('CONFIG_FILE') && file_exists(CONFIG_FILE)) {
            $this->config = include_once(CONFIG_FILE);
        }

        // Inject Config Container
        $config_container = ConfigContainer::component();
        foreach ($this->config as $config_name => $config_content) {
            $config_container->set($config_name, $config_content);
        }
        $this->config = [];
        Lb::app()->containers['config'] = $config_container;
    }
}
