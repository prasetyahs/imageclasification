<?php


function binary($data, $type = "")
{
    $data =  file_get_contents($data);
    $data = $type === "em" ? str_replace(" ", "space", preg_replace('/\s\s+/', 'space', $data)) . 'space' : $data;
    $u    = unpack('C*', $data);
    $bin = [];
    foreach ($u as $b) {
        array_push($bin, decbin($b));
    }
    return $bin;
}

function stego($data, $embed)
{

    $em = implode("", $embed);
    for ($i = 0; $i < strlen($em); $i++) {
        $data[$i][strlen($data[$i]) - 1] = $em[$i];
    }
    return $data;
}

function stegoGetHide($result)
{
    $bin = "";
    for ($i = 0; $i < 7; $i++) {
        $bin .= $result[$i][strlen($result[$i]) - 1];
    }
    return $bin;
}
// encode
function encoder($cover, $embed, $outputName)
{
    $embed = binary($embed, "em");
    $data = binary($cover);
    $result = stego($data, $embed);
    $i = 0;
    foreach ($result as $s) {
        $result[$i] = bindec($s);
        $i++;
    }
    $pack = pack("C*", ...$result);
    file_put_contents($outputName, $pack);
}

// decode
function decoder($fileSteg, $stegKey)
{
    $data = binary($fileSteg);
    $binaryString = [];
    $tmpStr = '';
    for ($i = 0; $i < count($data); $i++) {
        $tmpStr .= $data[$i][strlen($data[$i]) - 1];
        if (strlen($tmpStr) >= 7) {
            array_push($binaryString, $tmpStr);
            $tmpStr = '';
        }
    }
    $result = '';
    foreach ($binaryString as $s) {
        $result .= chr(bindec(stegoGetHide($s)));
    }
    $explodeRs = explode("space", $result);
    if ($stegKey === $explodeRs[count($explodeRs) - 2]) {
        echo json_encode([
            "result"=>str_replace('space', " ", explode($stegKey, $result)[0]),
            "message"=>"Berhasil ambil pesan"
        ]);
    }else{
        echo json_encode([
            "message"=>"Password Tidak sesuai!"
        ]);
    }
}

encoder("test.mp3", "test.txt", "udud.mp3");
decoder("udud.mp3", "adaadasaja");
