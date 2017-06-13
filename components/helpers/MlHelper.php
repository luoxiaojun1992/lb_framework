<?php

namespace lb\components\helpers;

use lb\BaseClass;
use Phpml\Classification\KNearestNeighbors;
use Phpml\Dataset\CsvDataset;
use Phpml\Preprocessing\Normalizer;
use Phpml\Classification\SVC;
use Phpml\SupportVectorMachine\Kernel;

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

    public static function svc($dataSet, $featureNum, $ignoreHeader = false)
    {
        $dataSet = new CsvDataset($dataSet, $featureNum, $ignoreHeader);

        $samples = $dataSet->getSamples();
        $normalizer = new Normalizer();
        $normalizer->fit($samples);
        $normalizer->transform($samples);

        $classifier = new SVC(Kernel::LINEAR, $cost = 1000);
        $classifier->train($samples, $dataSet->getTargets());

        return $classifier;
    }
}
