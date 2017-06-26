<?php

namespace lb\components\widget;

use lb\components\traits\Singleton;

class Grid extends Base
{
    use Singleton;

    protected $dataProvider;
    protected $options;
    protected $htmlOptions = [];

    /**
     * @return object
     */
    public static function component()
    {
        if (static::$instance instanceof static) {
            $instance = static::$instance;
            $instance->setDataProvider(null);
            $instance->setOptions(null);
            $instance->setHtmlOptions([]);
            return $instance;
        } else {
            return (static::$instance = new static());
        }
    }

    /**
     * @return mixed
     */
    public function getDataProvider()
    {
        return $this->dataProvider;
    }

    /**
     * @param mixed $dataProvider
     * @return $this;
     */
    public function setDataProvider($dataProvider)
    {
        $this->dataProvider = $dataProvider;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param mixed $options
     * @return $this;
     */
    public function setOptions($options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @return array
     */
    public function getHtmlOptions(): array
    {
        return $this->htmlOptions;
    }

    /**
     * @param array $htmlOptions
     * @return $this;
     */
    public function setHtmlOptions(array $htmlOptions)
    {
        $this->htmlOptions = $htmlOptions;
        return $this;
    }

    public function render()
    {
        $grid_tpl = <<<Grid
<table %s>
    <thead>%s</thead>
    <tbody>%s</tbody>
</table>
Grid;

        $tableHtmlOptions = [];
        if ($this->getHtmlOptions()) {
            foreach ($this->getHtmlOptions() as $attribute_name => $attribute_value) {
                $tableHtmlOptions[] = "$attribute_name=\"{$attribute_value}\"";
            }
        }
        $tableHtmlOptionHtml = implode(' ', $tableHtmlOptions);

        $thead_tpl = '<tr>%s</tr>';
        $thead = [];
        foreach ($this->getOptions() as $option) {
            if (isset($option['label'])) {
                $thead[] = "<td>{$option['label']}</td>";
            } else {
                $thead[] = '<td>Not Set</td>';
            }
        }
        $thead_html = sprintf($thead_tpl, implode('', $thead));

        $tbody = [];
        foreach ($this->getDataProvider() as $data) {
            $tmpStr = '<tr>';
            foreach ($this->getOptions() as $option) {
                if (isset($option['attribute'])) {
                    $data_value = $data->{$option['attribute']};
                    if ($data_value !== false) {
                        if (isset($option['value'])) {
                            $data_value = $option['value']($data_value, $data);
                            $tmpStr .= "<td>{$data_value}</td>";
                        } else {
                            $tmpStr .= "<td>{$data_value}</td>";
                        }
                    } else {
                        $tmpStr .= '<td>Not Set</td>';
                    }
                } else {
                    if (isset($option['value'])) {
                        $data_value = $option['value']($data);
                        $tmpStr .= "<td>{$data_value}</td>";
                    } else {
                        $tmpStr .= '<td>Not Set</td>';
                    }
                }
            }
            $tmpStr .= '</tr>';
            $tbody[] = $tmpStr;
        }
        $tbody_html = implode('', $tbody);

        $grid_html = sprintf($grid_tpl, $tableHtmlOptionHtml, $thead_html, $tbody_html);

        return $grid_html;
    }
}
