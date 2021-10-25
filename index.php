<?php

require_once __DIR__ . '/vendor/autoload.php';

use Phpml\Classification\KNearestNeighbors;
use Phpml\Math\Distance\Euclidean;
use Phpml\Metric\Regression;
use Phpml\CrossValidation\RandomSplit;
use Phpml\DataSet\ArrayDataSet;

function getArrayOfPixelsFromFile($source)
{
    $image = imagecreatefromjpeg($source); // imagecreatefromjpeg/png/
    $image = imagescale($image, 28, 28);
    $width = imagesx($image);
    $height = imagesy($image);
    $colors = array();
    for ($y = 0; $y < $height; $y++) {
        $y_array = array();
        for ($x = 0; $x < $width; $x++) {
            $rgb = imagecolorat($image, $x, $y);
            //echo $rgb." = ".decbin($rgb),"<br>";
            //Seleciona os primeiros dois bytes que representam vermelho
            $r = ($rgb >> 16) & 0xFF;
            //Seleciona os dois bytes do meio que representam o verde
            $g = ($rgb >> 8) & 0xFF;
            //Seleciona os dois últimos bytes que representam o azul
            $b = $rgb & 0xFF;
            $x_array = array($r, $g, $b);
            $y_array[] = $x_array;
        }
        $colors[] = $y_array;
    }
    return $colors;
}

function array_to1d($a)
{
    $out = array();
    foreach ($a as $b) {
        foreach ($b as $c) {
            if (isset($c)) {
                $out[] = $c;
            }
        }
    }
    return $out;
}

function loadImagePredict()
{
    $imgTmp = [];
    $path    = 'dtset';
    $files = scandir($path);
    $files = array_diff(scandir($path), array('.', '..'));
    foreach ($files as $v) {
        array_push($imgTmp, $path . "/" . $v);
    }
    return $imgTmp;
}

function loadData()
{
    $dataTmp = [];
    $path    = 'dtset';
    $files = scandir($path);
    $files = array_diff(scandir($path), array('.', '..'));
    foreach ($files as $v) {
        array_push($dataTmp, array_to1d(array_to1d(getArrayOfPixelsFromFile($path . "/" . $v))));
    }
    return $dataTmp;
}
function labeling()
{
    $path    = 'dtset';
    $files = scandir($path);
    $labels = [];
    $files = array_diff(scandir($path), array('.', '..'));
    foreach ($files as $v) {
        array_push($labels, str_split(explode("a", $v)[2])[0]);
    }
    return $labels;
}

function getDistance($imageInput)
{

    $distanceTmp = [];
    $path    = 'dtset';
    $files = scandir($path);
    $files = array_diff(scandir($path), array('.', '..'));
    $euclidean = new Euclidean();
    foreach ($files as $v) {
        $dataset =  array_to1d(array_to1d(getArrayOfPixelsFromFile($path . "/" . $v)));
        $result = $euclidean->distance($dataset, $imageInput);
        array_push($distanceTmp, $result);
    }
    return $distanceTmp;
}

function splitData($dataset)
{
    $dataset = new RandomSplit($dataset, 0.3, 1234);
    return $dataset;
}

function knnProcess($imageInput)
{
    $classifier = new KNearestNeighbors($k = 2, new Euclidean());
    $classifier->train(loadData(), labeling());
    $distance = getDistance($imageInput);
    $predict = $classifier->predict($imageInput);
    $minDistance = min($distance);
    $index = array_search($minDistance, $distance);
    $result =  [
        "predict" => $predict,
        "index" => $index
    ];
    return $result;
}
function getLocalIp()
{
    return gethostbyname(trim(`hostname`));
}

// -- cross validation random split --
// $dataset = new ArrayDataset(loadData(), labeling());
// $dataset = new RandomSplit($dataset, 0.3);
// $testSample = $dataset->getTestSamples();
// $testLabels =  $dataset->getTestLabels();
// $trainSample = $dataset->getTrainSamples();
// $trainLabel = $dataset->getTrainLabels();
// $classifier = new KNearestNeighbors($k = 3, new Euclidean());
// $classifier->train($trainSample, $trainLabel);
// $validation  = [];
// $i = 0;
// foreach ($testSample as $t) {
//     $predict = $classifier->predict($t) === $testLabels[$i] ? 1 : 0;
//     array_push($validation, $predict);
//     $i++;
// }

// print_r($validation);

header('Content-Type: application/json');
$imageInput = array_to1d(array_to1d(getArrayOfPixelsFromFile("dtset/kanaCHI14.jpg")));
$result = knnProcess($imageInput);
$predictImageSum = array_sum(array_to1d(array_to1d(getArrayOfPixelsFromFile(loadImagePredict()[$result['index']]))));
$imageInputSum = array_sum($imageInput);
$percent = ($imageInputSum / $predictImageSum) * 100;
echo json_encode([
    "image_prediction" => "http://" . getLocalIp() . "/imageClassifitcation/" . loadImagePredict()[$result['index']],
    "prediction" => $result['predict'],
    "similarity_percentage" => $percent
]);
