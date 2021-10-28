<?php

// file_put_contents("hehe.mp3", pack('H*', bin2hex(implode('',$coverData))));

// function encode($mp3)
// {
//     $data = fopen($mp3, 'r');
//     $size = filesize($mp3);
//     $contents = fread($data, $size);
//     $binaryArr = "";
//     for ($i = 0; $i < $size; $i++) {
//         $binaryArr .= $contents[$i];
//     }
//     return $binaryArr;
// }
// function Passaaa($request, $type, $password)
// {
//     $iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);
//     $data = $request;
//     if ($type == 'en') {
//         $enc = openssl_encrypt($data, 'aes-256-cbc', $password, 0, $iv);
//     } else {
//         $enc = openssl_decrypt($data, 'aes-256-cbc', $password, 0, $iv);
//     }
//     return $enc;
// }

// $embed = encode("test.mp3");
// $cover = encode("test.txt");

// for ($i = 0; $i < strlen($embed); $i++) {
//     $cover[$i] = $embed[$i];
//     if ($i > strlen($embed)) {
//         $cover[$i + 1] = '-';
//     }
// }

// file_put_contents("hehe.mp3", pack('H*', bin2hex($cover)));

// $dataDecrypt = encode("hehe.mp3");
// $resultDecrypt = Passaaa(explode('-', $dataDecrypt)[0] . '===', 'dec', "test");
// print_r($resultDecrypt);

// function base64bin($str)
// {
//     $result = '';
//     $str = base64_decode($str);
//     $len = strlen($str);
//     for ($n = 0; $n < $len; $n++) {
//         $result .= str_pad(decbin(ord($str[$n])), 8, '0', STR_PAD_LEFT);
//     }
//     return $result;
// }

// --embed--
// $data = file_get_contents('test.txt');
// $u   = unpack('C*', $data);

//--cover--
// $data = file_get_contents('test.mp3');
// $u    = unpack('C*', $data);
// $index = 0;
// $resultBinary = [];
// foreach ($u  as $s) {
//     array_push($resultBinary,decbin($s));
// }
// print_r();
// die;
// $bin  = pack('C*', $u);
// print_r($bin);

// function toBinary($fileName)
// {
//     $data = file_get_contents($fileName);
//     $u    = unpack('C*', $data);
//     $resultBinary = [];
//     foreach ($u  as $s) {
//         array_push($resultBinary, decbin($s));
//     }
//     return $resultBinary;
// }


// function getHide($cover)
// {
//     $data = [];
//     $i = 0;
//     foreach ($cover as $c) {
//         array_push($data, $c[strlen($c) - 1]);
//         if ($i == 4) {
//             return $data;
//         }
//         $i++;
//     }
//     return $data;
// }

// function stegann($cover, $embed)
// {
//     $stringBinary = implode('', $embed);
//     print_r($stringBinary);
//     for ($i = 0; $i < strlen($stringBinary); $i++) {
//         $lenCover = strlen($cover[$i]) - 1;
//         $cover[$i][$lenCover] = $stringBinary[$i];
//         // echo ($cover[$i][$lenCover] . "\n");
//         // echo ("embed"."");
//         echo $stringBinary[$i];
//         $cover[$i] = $cover[$i];
//     }
//     return $cover;
// }
// $cover = toBinary("test.mp3");
// $embed = toBinary("test.txt");
// $resultDec = stegann($cover, $embed);
// $result = [];
// foreach ($resultDec as $s) {
//     array_push($result, bindec($s));
// }
// $bin  = pack('C*', ...$result);
// file_put_contents("text.mp3", $bin);

// $embed = toBinary("text.mp3");
// // $embed = toBinary("test.txt");
// // print_r($embed);
// $data = getHide($embed);
// $result = [];

// foreach ($data as $s) {
//     array_push($result, bindec($s));
// }
// $bin  = pack('C*', ...$result);
// print_r($bin);

// $dec = [];
// foreach ($embed  as $e) {
//     array_push($dec, decbin($e));
// }
// // print_r($dec);
// $bin  = pack('C*', ...$dec);
// print_r($bin);

function toBin($fileName)
{
    $data = file_get_contents($fileName);
    $u    = unpack('C*', $data);
    $bin = [];
    foreach ($u as $a) {
        array_push($bin, decbin($a));
    }
    return $bin;
}
function toDec($bin)
{
    $dec = [];
    foreach ($bin as $b) {
        array_push($dec, bindec($b));
    }
    return $dec;
}

function repack($dec)
{
    $pack  = pack('C*', ...$dec);
    file_put_contents("hehe.mp3", $pack);
}

function stegano($coverBin, $embedBin)
{
    $embedString = implode("", $embedBin);
    for ($i = 0; $i < strlen($embedString); $i++) {
        $coverBin[$i][strlen($coverBin[$i]) - 1] = $embedString[$i];
    }
    return $coverBin;
}


function decode($cover)
{
    $result = [];
    foreach ($cover as $c) {
        array_push($result, $c[strlen($c) - 1]);
    }
    return $result;
}
// -- encode
$embedBin = toBin("test.txt");
$coverBin = toBin("test.mp3");
// print_r($coverBin);
$data = stegano($coverBin, $embedBin);
// $dec = toDec($data);
// $bin  = pack('C*', ...$dec);
// file_put_contents("hehetest.mp3", $bin);
// $coverBin = toBin("hehetest.mp3");
// foreach (decode(toBin("hehetest.mp3")) as $v) {
//     print_r($v);
// print_r(chr($v));
// }
// print_r(chr(decode(toBin("hehetest.mp3")));

$data = bindec("1001101");
$bin  = pack('C*', $data);

print_r(chr("10010011"));
