<?php

namespace lb\components\helpers;

use lb\BaseClass;
use Phpml\Dataset\CsvDataset;
use Phpml\NeuralNetwork\Network\MultilayerPerceptron;
use Phpml\NeuralNetwork\Training\Backpropagation;

class MlHelper extends BaseClass
{
    public static function mlp($dataSet, $featureNum, $labelNum, $ignoreHeader = false)
    {
        $dataSet = new CsvDataset($dataSet, $featureNum, $ignoreHeader);

        $model = new Backpropagation(new MultilayerPerceptron([$featureNum, $featureNum, $labelNum]));
        $model->train($dataSet->getSamples(), $dataSet->getTargets());
        return $model;
    }
}
