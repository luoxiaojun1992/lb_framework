<?php

namespace lb\components\helpers;

use lb\BaseClass;
use Phpml\Classification\KNearestNeighbors;
use Phpml\Dataset\CsvDataset;
use Phpml\Preprocessing\Normalizer;

class MlHelper extends BaseClass
{
    public static function knn($dataSet, $featureNum, $ignoreHeader = false)
    {
        $dataSet = new CsvDataset($dataSet, $featureNum, $ignoreHeader);

        $samples = $dataSet->getSamples();
        $normalizer = new Normalizer();
        $normalizer->fit($samples);
        $normalizer->transform($samples);

        $classifier = new KNearestNeighbors();
        $classifier->train($samples, $dataSet->getTargets());

        return $classifier;
    }
}
