<?php

namespace lb\components\traits\lb;

use lb\Lb;

trait Config
{
    // Get App Root Directory
    public function getRootDir()
    {
        if ($this->isSingle()) {
            if (isset($this->containers['config'])) {
                return $this->containers['config']->get('root_dir');
            }
        }
        return '';
    }

    // Get App Name
    public function getName()
    {
        if ($this->isSingle()) {
            if (isset($this->containers['config'])) {
                return $this->containers['config']->get('name');
            }
        }
        return '';
    }

    // Get Restful Api Config
    public function getRest()
    {
        if ($this->isSingle()) {
            if (isset($this->containers['config'])) {
                return $this->containers['config']->get('rest');
            }
        }
        return false;
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
        if ($this->isSingle()) {
            if (isset($this->containers['config'])) {
                return trim($this->containers['config']->get('cdn_host'), '/');
            }
        }
        return '';
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
        if ($this->isSingle()) {
            if (isset($this->containers['config'])) {
                $asset_config = $this->containers['config']->get('assets');
                if ($asset_config) {
                    if (isset($asset_config[$controller_id][$template_id]['js'])) {
                        $js_files = $asset_config[$controller_id][$template_id]['js'];
                    }
                }
            }
        }
        return $js_files;
    }

    // Get Css Files
    public function getCssFiles($controller_id, $template_id)
    {
        $css_files = [];
        if ($this->isSingle()) {
            if (isset($this->containers['config'])) {
                $asset_config = $this->containers['config']->get('assets');
                if ($asset_config) {
                    if (isset($asset_config[$controller_id][$template_id]['css'])) {
                        $css_files = $asset_config[$controller_id][$template_id]['css'];
                    }
                }
            }
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
     * Init Configuration
     */
    protected function initConfig()
    {
        if (defined('CONFIG_FILE') && file_exists(CONFIG_FILE)) {
            $this->config = include_once(CONFIG_FILE);
        }

        // Inject Config Container
        $config_container = Config::component();
        foreach ($this->config as $config_name => $config_content) {
            $config_container->set($config_name, $config_content);
        }
        $this->config = [];
        Lb::app()->containers['config'] = $config_container;
    }
}
