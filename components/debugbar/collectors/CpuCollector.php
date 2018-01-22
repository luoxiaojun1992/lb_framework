<?php

namespace lb\components\debugbar\collectors;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;

class CpuCollector extends DataCollector implements Renderable
{
    /**
     * @return array
     */
    public function collect()
    {
        $cpuLoadAvg = implode(',', sys_getloadavg());
        return array(
            'peak_usage' => $cpuLoadAvg,
            'peak_usage_str' => $cpuLoadAvg
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'cpu';
    }

    /**
     * @return array
     */
    public function getWidgets()
    {
        return array(
            "memory" => array(
                "icon" => "cogs",
                "tooltip" => "CPU Load Avg",
                "map" => "memory.peak_usage_str",
                "default" => "'0,0,0'"
            )
        );
    }
}
