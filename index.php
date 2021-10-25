<?php

require_once __DIR__ . '/vendor/autoload.php';

use Phpml\Classification\KNearestNeighbors;
use Phpml\Math\Distance\Euclidean;
use Phpml\Metric\Regression;

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

function knnProcess()
{
    $imageInput = array_to1d(array_to1d(getArrayOfPixelsFromFile("dtset/kanaBA1.jpg")));
    $classifier = new KNearestNeighbors($k = 2,);
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
// $key = array_search(min(getDistance()), getDistance());
$result = knnProcess();
// echo '<img src= "' . loadImagePredict()[$result['index']] . '"alt="Girl in a jacket" width="500" height="600">';
// echo "<pre>";
// echo $result['predict'];

function getLocalIp()
{
    return gethostbyname(trim(`hostname`));
}
header('Content-Type: application/json');
echo json_encode([
    "gambar" => "http://" . getLocalIp() . "/imageClassifitcation/" . loadImagePredict()[$result['index']],
    "prediksi" => $result['predict'],


]);
