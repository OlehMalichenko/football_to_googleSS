<?php
require_once __DIR__ . '\vendor\autoload.php';

function curlstart($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, TRUE);
    curl_setopt($ch, CURLOPT_ACCEPT_ENCODING, "*");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:67.0) Gecko/20100101 Firefox/67.0');
    curl_setopt($ch, CURLOPT_COOKIEJAR, __DIR__.'/cookie.txt');
    curl_setopt($ch, CURLOPT_COOKIEFILE, __DIR__.'/cookie.txt');
    $html = curl_exec($ch);
    //var_dump(curl_getinfo($ch));
    curl_close($ch);

    if ($html != FALSE) {
        return $html;
    }else{
        return FALSE;
    }
}

